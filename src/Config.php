<?php

namespace PHPUnitBenchmarkData;

use Swaggest\JsonSchema\Constraint\Properties;
use Swaggest\JsonSchema\Schema;
use Swaggest\JsonSchema\Structure\ClassStructure;

class Config extends ClassStructure
{
    public $calcPerformanceIndex;

    public $resultPrecision;
    public $resultFilename;

    /** @var TestConfig */
    public $defaultTestConfig;

    /** @var TestConfig[] map of test name to config */
    public $testConfigs;

    /**
     * @param Properties|static $properties
     * @param Schema $ownerSchema
     * @throws \Exception
     * @throws \Swaggest\JsonSchema\InvalidValue
     */
    public static function setUpProperties($properties, Schema $ownerSchema)
    {
        $properties->calcPerformanceIndex = Schema::boolean()->setDefault(true)
            ->setDescription('Calc host performance index');

        $properties->resultPrecision = Schema::integer()->setDefault(5)
            ->setDescription('Result JSON precision');

        $properties->resultFilename = Schema::string()->setDefault('benchmark-result.json')
            ->setDescription('Result filename');

        $defaultTestConfig = new TestConfig();
        $defaultTestConfig->numIterations = 100;
        $properties->defaultTestConfig = clone TestConfig::schema();
        $properties->defaultTestConfig->setDefault(TestConfig::export($defaultTestConfig))
            ->setDescription('Default configuration');

        $properties->testConfigs = Schema::object()->setAdditionalProperties(TestConfig::schema())
            ->setDescription('Test settings');
    }

    /**
     * @return static
     * @throws \Exception
     * @throws \Swaggest\JsonSchema\Exception
     * @throws \Swaggest\JsonSchema\InvalidValue
     */
    public static function load()
    {
        if (file_exists('phpunit-bench.json')) {
            $config = Config::import(json_decode(file_get_contents('phpunit-bench.json')));
        } else {
            $config = Config::import(new \stdClass);
        }
        return $config;
    }
}