<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
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
        $firstModel = ucfirst($this->argument('firstModel'));
        $secondModel = ucfirst($this->argument('secondModel'));
        $reverse = $this->option('reverse');
        $namespace = $this->option('namespace') ?? config('eloqrait.namespace', 'App\Models\Traits');
        $modelNamespace = $this->option('model-namespace') ?? config('eloqrait.model_namespace', 'App\Models');

        $this->generateRelations($type, $firstModel, $secondModel, $namespace, $modelNamespace, $reverse);
        $this->info('Relation created successfully.');
    }

    private function generateRelations($type, $firstModel, $secondModel, $namespace, $modelNamespace, $reverse)
    {
        $relationStub = File::get($this->getStubPath($type));
        $this->createModelFile($firstModel, $modelNamespace);
        $this->createModelFile($secondModel, $modelNamespace);
        $this->appendRelation($type, $firstModel, $secondModel, $modelNamespace, $relationStub);

        if ($reverse) {
            $reverseStub = File::get($this->getStubPath('belongsTo'));
            $this->appendRelation('belongsTo', $secondModel, $firstModel, $modelNamespace, $reverseStub);
        }
    }

    private function getStubPath($type)
    {
        return __DIR__ . '/../stubs/' . $type . '.stub';
    }

    private function createModelFile($modelName, $modelNamespace)
    {
        $filePath = app_path("Models/{$modelName}.php");
        if (!File::exists($filePath)) {
            $modelTemplate = "<?php\n\nnamespace {$modelNamespace};\n\nuse Illuminate\Database\Eloquent\Model;\n\nclass {$modelName} extends Model\n{\n    //\n}\n";
            File::put($filePath, $modelTemplate);
            $this->info("Model created at: {$filePath}");
        }
    }

    private function appendRelation($type, $modelName1, $modelName2, $modelNamespace, $stub)
    {
        $replacements = [
            '{{ modelName1 }}' => $modelName1,
            '{{ modelName2 }}' => $modelName2,
            '{{ modelName1Plural }}' => Str::plural($modelName1),
            '{{ modelName2Plural }}' => Str::plural($modelName2),
            '{{ relationName }}' => Str::camel(Str::plural($modelName2)),
            '{{ reverseRelationName }}' => Str::camel($modelName1),
            '{{ reverseModelName }}' => $modelName1,
            '{{ namespace }}' => $modelNamespace,
            '{{ modelNamespace }}' => $modelNamespace
        ];

        $content = str_replace(array_keys($replacements), array_values($replacements), $stub);
        $filePath = app_path("Models/{$modelName1}.php");
        File::append($filePath, "\n\n" . $content);
    }
}
