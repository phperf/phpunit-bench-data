<?php

namespace PHPUnitBenchmarkData;

use Swaggest\JsonSchema\Constraint\Format;
use Swaggest\JsonSchema\Schema;
use Swaggest\JsonSchema\Structure\ClassStructure;

/**
 * @property int $numIterations
 * @property bool $skipBenchmark
 * @property bool $forceBenchmark
 * @property string $testCaseNameRegex
 * @property string $testNameRegex
 * @property string $dataNameRegex
 */
class TestConfig extends ClassStructure
{
    /**
     * @param \Swaggest\JsonSchema\Constraint\Properties|static $properties
     * @param Schema $ownerSchema
     */
    public static function setUpProperties($properties, Schema $ownerSchema)
    {
        $properties->numIterations = Schema::integer()
            ->setDescription('Number of iterations');

        $properties->skipBenchmark = Schema::boolean()
            ->setDescription('Skip test benchmark, overrides default');

        $properties->skipBenchmark = Schema::boolean()
            ->setDescription('Force test benchmark, overrides default');

        $properties->testCaseNameRegex = Schema::string()->setFormat(Format::REGEX)
            ->setDescription('Regular expression to filter test case class name');

        $properties->testNameRegex = Schema::string()->setFormat(Format::REGEX)
            ->setDescription('Regular expression to filter test name');

        $properties->dataNameRegex = Schema::string()->setFormat(Format::REGEX)
            ->setDescription('Regular expression to filter data name');
    }
}