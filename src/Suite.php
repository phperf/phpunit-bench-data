<?php

namespace PHPUnitBenchmarkData;

use Swaggest\JsonSchema\Constraint\Properties;
use Swaggest\JsonSchema\Schema;
use Swaggest\JsonSchema\Structure\ClassStructure;

class Suite extends ClassStructure
{
    public $hostPerformanceIndex;
    public $commitHash;
    public $branch;
    public $tag;
    public $totalTime = 0;
    public $totalSpeed;
    public $totalIterations = 0;
    /** @var TestCase[] */
    public $testCases;

    /** @var Config */
    private $config;

    /**
     * Suite constructor.
     * @throws \Exception
     * @throws \Swaggest\JsonSchema\Exception
     * @throws \Swaggest\JsonSchema\InvalidValue
     */
    public function __construct()
    {
        $this->config = Config::load();
    }

    public function jsonSerialize()
    {
        $this->totalSpeed = $this->totalTime / $this->hostPerformanceIndex;
        return parent::jsonSerialize();
    }

    /**
     * @param Properties|static $properties
     * @param Schema $ownerSchema
     */
    public static function setUpProperties($properties, Schema $ownerSchema)
    {
        $properties->hostPerformanceIndex = Schema::number();
        $properties->totalTime = Schema::number();
        $properties->totalSpeed = Schema::number();
        $properties->totalIterations = Schema::integer();
        $properties->testCases = Schema::object()
            ->setAdditionalProperties(TestCase::schema())
            ->setUseObjectAsArray(true);
    }

    /**
     * @param $filePath
     * @return static
     * @throws \Exception
     * @throws \Swaggest\JsonSchema\Exception
     * @throws \Swaggest\JsonSchema\InvalidValue
     */
    public static function loadFromJsonFile($filePath)
    {
        return Suite::import(json_decode(file_get_contents($filePath)));
    }

    /**
     * @throws \Swaggest\JsonSchema\InvalidValue
     * @throws \Exception
     */
    public function save()
    {
        $benchmarkData = self::export($this);
        $precision = ini_get('precision');
        ini_set('precision', $this->config->resultPrecision);
        file_put_contents($this->config->resultFilename,
            json_encode($benchmarkData, JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES));
        ini_set('precision', $precision);
    }

    public function getNumIterations($testCaseName, $testName, $dataName) {
        $testConfig = clone $this->config->defaultTestConfig;
        if ($this->config->testConfigs !== null) {
            foreach ($this->config->testConfigs as $testConfigItem) {
                $applies = true;
                if ($applies && $testConfigItem->testCaseNameRegex) {
                    $applies = preg_match('/' . $testConfigItem->testCaseNameRegex . '/i', $testCaseName);
                }
                if ($applies && $testConfigItem->testNameRegex) {
                    $applies = preg_match('/' . $testConfigItem->testNameRegex . '/i', $testName);
                }
                if ($applies && $testConfigItem->dataNameRegex) {
                    $applies = preg_match('/' . $testConfigItem->dataNameRegex . '/i', $dataName);
                }

                if ($applies) {
                    if ($testConfigItem->skipBenchmark !== null) {
                        $testConfig->skipBenchmark = $testConfigItem->skipBenchmark;
                    }
                    if ($testConfigItem->forceBenchmark !== null) {
                        $testConfig->forceBenchmark = $testConfigItem->forceBenchmark;
                    }
                    if ($testConfigItem->numIterations !== null) {
                        $testConfig->numIterations = $testConfigItem->numIterations;
                    }
                }
            }
        }
        if ($testConfig->forceBenchmark) {
            return $testConfig->numIterations;
        } elseif ($testConfig->skipBenchmark) {
            return 0;
        } else {
            return $testConfig->numIterations;
        }
    }

    public function addResult(Result $result, $testName, $testCaseName)
    {
        if ($this->hostPerformanceIndex === null && $this->config->calcPerformanceIndex) {
            $this->hostPerformanceIndex = PerformanceIndex::measure();
        }

        $testCase = &$this->testCases[$testCaseName];
        if ($testCase === null) {
            $testCase = new TestCase();
            $testCase->name = $testCaseName;
        }

        $test = &$testCase->tests[$testName];
        if (null === $test) {
            $test = new Test();
            $test->name = $testName;
        }

        if (empty($result->dataName)) {
            $result->dataName = 'default';
        }

        $test->results[$result->dataName] = $result;

        $test->totalSpent += $result->timeSpent;
        $test->totalIterations += $result->iterations;

        $testCase->totalTime += $result->timeSpent;
        $testCase->totalIterations += $result->iterations;

        $this->totalIterations += $result->iterations;
        $this->totalTime += $result->timeSpent;
    }


}