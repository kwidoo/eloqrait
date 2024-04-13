<?php

namespace App\Services;

use Illuminate\Support\Str;

class ModelNames
{
    public $firstModelName;
    public $secondModelName;
    public $firstPlural;
    public $secondPlural;
    public $relationName;
    public $reverseRelationName;
    public $reverseModelName;

    public function __construct(
        public string $first,
        public string $second,
        public string $relationType,
        public bool $reverse = false,
        public ?string $namespace = null,
        public ?string $modelNamespace = null
    ) {
        $this->firstPlural = Str::plural($this->first);
        $this->secondPlural = Str::plural($this->second);
        $this->relationName = lcfirst($this->firstPlural);
        $this->reverseRelationName = lcfirst($this->secondPlural);
        $this->reverseModelName = ucfirst($this->first);
    }

    public function yieldProperties()
    {
        foreach (get_object_vars($this) as $propertyName => $propertyValue) {
            yield $propertyName;
        }
    }
}
