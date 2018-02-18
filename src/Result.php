<?php

namespace PHPUnitBenchmarkData;

use Swaggest\JsonSchema\Constraint\Properties;
use Swaggest\JsonSchema\Schema;
use Swaggest\JsonSchema\Structure\ClassStructure;

class Result extends ClassStructure
{
    public $dataName;
    public $timeSpent;
    public $iterations;

    /**
     * Result constructor.
     * @param $dataName
     * @param $timeSpent
     * @param $iterations
     */
    public function __construct($dataName = null, $timeSpent = null, $iterations = null)
    {
        $this->dataName = $dataName;
        $this->timeSpent = $timeSpent;
        $this->iterations = $iterations;
    }


    /**
     * @param Properties|static $properties
     * @param Schema $ownerSchema
     */
    public static function setUpProperties($properties, Schema $ownerSchema)
    {
        $properties->timeSpent = Schema::number();
        //$properties->dataName = Schema::string();
        $properties->iterations = Schema::integer();
    }


}