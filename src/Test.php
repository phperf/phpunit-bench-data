<?php

namespace PHPUnitBenchmarkData;

use Swaggest\JsonSchema\Constraint\Properties;
use Swaggest\JsonSchema\Schema;
use Swaggest\JsonSchema\Structure\ClassStructure;

class Test extends ClassStructure
{
    public $name;
    public $totalSpent = 0;
    public $totalIterations = 0;
    /** @var Result[] */
    public $results;

    /**
     * @param Properties|static $properties
     * @param Schema $ownerSchema
     */
    public static function setUpProperties($properties, Schema $ownerSchema)
    {
        $properties->totalSpent = Schema::number();
        $properties->totalIterations = Schema::integer();
        $properties->results = Schema::object()
            ->setAdditionalProperties(Result::schema())
            ->setUseObjectAsArray(true);
    }


}