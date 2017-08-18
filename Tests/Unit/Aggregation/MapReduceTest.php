<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Aggregation;


use Cundd\Stairtower\Tests\Unit\AbstractDatabaseBasedCase;
use Cundd\Stairtower\Constants;
use Cundd\Stairtower\Domain\Model\DocumentInterface;
use Cundd\Stairtower\Meta\Database\Property\Description;
use Cundd\Stairtower\Utility\GeneralUtility;
use stdClass;

/**
 * Tests for MapReduce
 *
 * @package Cundd\Stairtower\MapReduce
 */
class MapReduceTest extends AbstractDatabaseBasedCase
{
    /**
     * @var \Cundd\Stairtower\Aggregation\MapReduceInterface
     */
    protected $fixture;

    /**
     * @var \Cundd\Stairtower\DataAccess\Coordinator
     */
    protected $coordinator;

    protected function setUp()
    {
        /**
         * @param DocumentInterface $document
         */
        $mapFunction = function ($document) {
        };

        /**
         * @param string $key
         * @param mixed  $values
         */
        $reduceFunction = function ($key, $values) {
        };

        $this->fixture     = new MapReduce($mapFunction, $reduceFunction);
        $this->coordinator = $this->getDiContainer()->get('\Cundd\Stairtower\DataAccess\Coordinator');
    }

    /**
     * @test
     */
    public function emptyTest()
    {
        $database = $this->getSmallPeopleDatabase();
        $result   = $this->fixture->perform($database);
        $this->assertInternalType('array', $result);
        $this->assertEmpty($result);
    }

    /**
     * @test
     */
    public function simpleTest()
    {
        /**
         * @param DocumentInterface $document
         */
        $mapFunction = function ($document) {
            $allProperties = array_keys($document->getData());
            foreach ($allProperties as $propertyKey) {
                /** @var MapReduce $this */
                $this->emit($propertyKey, 1);
            }
        };

        /**
         * @param string $key
         * @param mixed  $values
         * @return number
         */
        $reduceFunction = function ($key, $values) {
            return array_sum($values);
        };

        $this->fixture = new MapReduce($mapFunction, $reduceFunction);

        $database = $this->getSmallPeopleDatabase();
        $result = $this->fixture->perform($database);

        $this->assertInternalType('array', $result);
        $this->assertEquals(21, count($result));

        $key = '';
        foreach ($result as $key => $value) {
            if ($key === Constants::DATA_ID_KEY) {
                break;
            }
        }
        $this->assertEquals(Constants::DATA_ID_KEY, $key);
    }


    /**
     * @test
     */
    public function propertyDescriptionTest()
    {
        /**
         * @param DocumentInterface $document
         */
        $mapFunction = function ($document) {
            $allProperties = $document->getData();
            foreach ($allProperties as $propertyKey => $propertyValue) {
                /** @var MapReduce $this */
                $this->emit(
                    $propertyKey,
                    array(
                        'type'  => GeneralUtility::getType($propertyValue),
                        'count' => 1
                    )
                );
            }
        };

        /**
         * @param string $key
         * @param mixed  $values
         * @return Description
         */
        $reduceFunction = function ($key, $values) {
            $types = array();
            $count = 0;
            foreach ($values as $valueBlock) {
                $types[$valueBlock['type']] = true;
                $count += $valueBlock['count'];
            }
            $types = array_keys($types);
            return new Description($key, $types, $count);
        };


        $this->fixture = new MapReduce($mapFunction, $reduceFunction);

        //$database = $this->getSmallPeopleDatabase();
        $database = $this->coordinator->getDatabase('people');
        $result   = $this->fixture->perform($database);

        $this->assertInternalType('array', $result);
        $this->assertEquals(21, count($result));

        /** @var Description $description */
        foreach ($result as $description) {
            if ($description->getKey() === Constants::DATA_ID_KEY) {
                break;
            }
        }
        $this->assertEquals(Constants::DATA_ID_KEY, $description->getKey());
        $this->assertContains(Description::TYPE_STRING, $description->getTypes());
    }


    /**
     * @test
     */
    public function cacheWithoutChangeTest()
    {
        $mapInvocationCounter = 0;
        /**
         * @param DocumentInterface $document
         */
        $mapFunction = function ($document) use (&$mapInvocationCounter) {
            ++$mapInvocationCounter;
        };

        /**
         * @param string $key
         * @param mixed  $values
         * @return number
         */
        $reduceFunction = function ($key, $values) {
            return $values;
        };

        $this->fixture = new MapReduce($mapFunction, $reduceFunction);

        $result = $this->fixture->perform($this->getSmallPeopleDatabase());
        $this->assertInternalType('array', $result);
        $this->assertEmpty($result);
        $mapInvocationCounterFirstRun = $mapInvocationCounter;


        $result = $this->fixture->perform($this->getSmallPeopleDatabase());
        $this->assertInternalType('array', $result);
        $this->assertEmpty($result);
        $this->assertTrue($mapInvocationCounter === $mapInvocationCounterFirstRun);
    }

    /**
     * @test
     */
    public function cacheWithChangeTest()
    {
        $mapInvocationCounter = 0;
        /**
         * @param DocumentInterface $document
         */
        $mapFunction = function ($document) use (&$mapInvocationCounter) {
            ++$mapInvocationCounter;
        };

        /**
         * @param string $key
         * @param mixed  $values
         * @return number
         */
        $reduceFunction = function ($key, $values) {
            return $values;
        };

        $this->fixture = new MapReduce($mapFunction, $reduceFunction);

        $result = $this->fixture->perform($this->getSmallPeopleDatabase());
        $this->assertInternalType('array', $result);
        $this->assertEmpty($result);
        $mapInvocationCounterFirstRun = $mapInvocationCounter;

        $result = $this->fixture->perform($this->coordinator->getDatabase('people'));
        $this->assertInternalType('array', $result);
        $this->assertEmpty($result);
        $this->assertTrue($mapInvocationCounter != $mapInvocationCounterFirstRun);
    }

    /**
     * @test
     * @expectedException \Cundd\Stairtower\Exception\InvalidCollectionException
     */
    public function invalidNullInputTest()
    {
        $this->fixture->perform(null);
    }

    /**
     * @test
     * @expectedException \Cundd\Stairtower\Exception\InvalidCollectionException
     */
    public function invalidStringInputTest()
    {
        $this->fixture->perform('anything');
    }

    /**
     * @test
     * @expectedException \Cundd\Stairtower\Aggregation\Exception\InvalidEmitKeyException
     */
    public function invalidEmitKeyArrayTest()
    {
        $mapFunction    = function ($document) {
            /** @var MapReduce $this */
            $this->emit(
                array('this is not allowed'), true
            );
        };
        $reduceFunction = function ($key, $values) {
        };
        $this->fixture  = new MapReduce($mapFunction, $reduceFunction);
        $this->fixture->perform($this->getSmallPeopleDatabase());
    }

    /**
     * @test
     * @expectedException \Cundd\Stairtower\Aggregation\Exception\InvalidEmitKeyException
     */
    public function invalidEmitKeyObjectTest()
    {
        $mapFunction    = function ($document) {
            /** @var MapReduce $this */
            $this->emit(
                new stdClass(), true
            );
        };
        $reduceFunction = function ($key, $values) {
        };
        $this->fixture  = new MapReduce($mapFunction, $reduceFunction);
        $this->fixture->perform($this->getSmallPeopleDatabase());
    }

    /**
     * @test
     * @expectedException \Cundd\Stairtower\Aggregation\Exception\InvalidEmitKeyException
     */
    public function invalidEmitKeyEmptyTest()
    {
        $mapFunction    = function ($document) {
            /** @var MapReduce $this */
            $this->emit(
                '', true
            );
        };
        $reduceFunction = function ($key, $values) {
        };
        $this->fixture  = new MapReduce($mapFunction, $reduceFunction);
        $this->fixture->perform($this->getSmallPeopleDatabase());
    }
}
