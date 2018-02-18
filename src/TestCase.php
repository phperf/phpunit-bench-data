<?php

namespace PHPUnitBenchmarkData;

use Swaggest\JsonSchema\Constraint\Properties;
use Swaggest\JsonSchema\Schema;
use Swaggest\JsonSchema\Structure\ClassStructure;
use Swaggest\JsonSchema\Structure\ObjectItem;

class TestCase extends ClassStructure
{
    public $name;
    public $totalTime;
    public $totalIterations;
    /** @var Test[]|ObjectItem */
    public $tests;

    /**
     * @param Properties|static $properties
     * @param Schema $ownerSchema
     */
    public static function setUpProperties($properties, Schema $ownerSchema)
    {
        $properties->totalTime = Schema::number();
        $properties->totalIterations = Schema::integer();
        $properties->tests = Schema::create()
            ->setAdditionalProperties(Test::schema())
            ->setUseObjectAsArray(true);
    }
}