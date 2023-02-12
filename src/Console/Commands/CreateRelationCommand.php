<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\File;

class CreateRelationCommand extends Command
{
    protected $signature = 'create:relation {type : The type of the relation (hasmany, belongsto, hasone, belongstomany)}
                            {firstModel : The name of the first model}
                            {secondModel : The name of the second model}
                            {--reverse : Create a reverse relation}
                            {--namespace= : The namespace of the generated code}
                            {--model-namespace= : The namespace of the models}';

    protected $description = 'Create a new relation between two models';

    public function handle()
    {
        $type = $this->argument('type');
        $firstModel = $this->argument('firstModel');
        $secondModel = $this->argument('secondModel');
        $reverse = $this->option('reverse');
        $namespace = $this->option('namespace') ?? config('eloqrait.namespace', 'App\Models\Traits');;
        $modelNamespace = $this->option('model-namespace') ?? config('eloqrait.model_namespace', 'App\Models');

        $stub = File::get(__DIR__ . '/../stubs/' . $type . '.stub');
        $modelName1 = ucfirst($firstModel);
        $modelName2 = ucfirst($secondModel);
        $modelName1Plural = str_plural($modelName1);
        $modelName2Plural = str_plural($modelName2);
        $relationName = lcfirst($modelName2Plural);
        $reverseRelationName = lcfirst($modelName1);
        $reverseModelName = ucfirst($firstModel);

        $stub = str_replace(
            ['{{ modelName1 }}', '{{ modelName2 }}', '{{ modelName1Plural }}', '{{ modelName2Plural }}', '{{ relationName }}', '{{ reverseRelationName }}', '{{ reverseModelName }}', '{{ namespace }}', '{{ modelNamespace }}'],
            [$modelName1, $modelName2, $modelName1Plural, $modelName2Plural, $relationName, $reverseRelationName, $reverseModelName, $namespace, $modelNamespace],
            $stub
        );

        $filePath1 = app_path('Models/' . $modelName1 . '.php');
        $filePath2 = app_path('Models/' . $modelName2 . '.php');

        $this->createFileIfNotExists($filePath1);
        $this->createFileIfNotExists($filePath2);

        File::append($filePath1, "\n\n" . $stub);
        if (!$reverse) {
            File::append($filePath2, "\n\n" . $stub);
        } else {
            $reverseStub = File::get(__DIR__ . '/stubs/belongsTo.stub');
            $reverseStub = str_replace(
                ['{{ modelName1 }}', '{{ modelName2 }}', '{{ modelName1Plural }}', '{{ modelName2Plural }}', '{{ relationName }}', '{{ reverseRelationName }}', '{{ reverseModelName }}', '{{ namespace }}', '{{ modelNamespace }}'],
                [$modelName2, $modelName1, $modelName2Plural, $modelName1Plural, $reverseRelationName, $relationName, $reverseModelName, $namespace, $modelNamespace],
                $reverseStub
            );
            File::append($filePath2, "\n\n" . $reverseStub);
        }

        $this->info('Relation created successfully.');
    }

    private function createFileIfNotExists($filePath)
    {
        if (File::exists($filePath)) {
            return;
        }

        $modelNamespace = $this->option('model-namespace') ?? config('eloqrait.model_namespace', 'App\Models');
        $modelName = basename($filePath, '.php');
        $content = "<?php\n\nnamespace {$modelNamespace};\n\nuse Illuminate\Database\Eloquent\Model;\n\nclass {$modelName} extends Model\n{\n    //\n}\n";

        File::put($filePath, $content);

        $this->info("Model created at: {$filePath}");
    }

    private function createTrait($type, $firstModel, $secondModel, $reverse)
    {
        $traitNamespace = $this->argument('namespace') ?? config('eloqrait.namespace', 'App\Traits');
        $modelNamespace = $this->option('model-namespace') ?? config('eloqrait.models_namespace', 'App\Models');

        $traitName = Str::studly($type) . Str::studly($secondModel) . ($reverse ? 'Reverse' : '') . 'Trait';
        $stub = File::get(__DIR__ . '/stubs/' . $type . '.stub');

        $replacements = [
            '{{ traitNamespace }}' => $traitNamespace,
            '{{ modelNamespace }}' => $modelNamespace,
            '{{ traitName }}' => $traitName,
            '{{ firstModel }}' => $firstModel,
            '{{ secondModel }}' => $secondModel,
        ];

        $content = str_replace(array_keys($replacements), array_values($replacements), $stub);

        $fileName = $traitName . '.php';
        $filePath = base_path('app/Traits') . '/' . $fileName;
        $file = new SplFileInfo($filePath);
        if (!$file->isFile()) {
            File::put($filePath, $content);
            $this->info($fileName . ' created successfully!');
        } else {
            $this->error($fileName . ' already exists!');
        }
    }
}
