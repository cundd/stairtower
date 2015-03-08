<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 28.02.15
 * Time: 14:41
 */

namespace Cundd\PersistentObjectStore\Aggregation;


use Cundd\PersistentObjectStore\AbstractDatabaseBasedCase;
use Cundd\PersistentObjectStore\Constants;
use Cundd\PersistentObjectStore\Domain\Model\DocumentInterface;
use Cundd\PersistentObjectStore\Meta\Database\Property\Description;
use Cundd\PersistentObjectStore\Utility\GeneralUtility;

/**
 * Tests for Aggregator
 *
 * @package Cundd\PersistentObjectStore\Aggregator
 */
class AggregatorTest extends AbstractDatabaseBasedCase
{
    /**
     * @var \Cundd\PersistentObjectStore\Aggregation\AggregatorInterface
     */
    protected $fixture;

    /**
     * @var \Cundd\PersistentObjectStore\DataAccess\Coordinator
     */
    protected $coordinator;

    protected function setUp()
    {
        /**
         * @param DocumentInterface $document
         */
        $aggregateFunction = function ($document) {
        };

        $this->fixture = new Aggregator($aggregateFunction);
        $this->coordinator = $this->getDiContainer()->get('\Cundd\PersistentObjectStore\DataAccess\Coordinator');
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
        $aggregateFunction = function ($document) {
            $allProperties = array_keys($document->getData());
            foreach ($allProperties as $propertyKey) {
                /** @var Aggregator $this */
                $this->results[$propertyKey] = 1;
            }
        };

        $this->fixture = new Aggregator($aggregateFunction);

        //$database = $this->getSmallPeopleDatabase();
        $database = $this->coordinator->getDatabase('people');
        $result   = $this->fixture->perform($database);

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
        $aggregateFunction = function ($document) {
            $allProperties = $document->getData();
            foreach ($allProperties as $propertyKey => $propertyValue) {
                /** @var Aggregator $this */
                if (!isset($this->results[$propertyKey])) {
                    $type = GeneralUtility::getType($propertyValue);
                    if (!isset($this->results[$propertyKey])) {
                        $this->results[$propertyKey] = array(
                            'types' => array($type => true),
                            'count' => 1
                        );
                    } else {
                        $this->results[$propertyKey]['types'][$type] = true;
                        ++$this->results[$propertyKey]['count'];
                    }
                }
            }
        };

        $this->fixture = new Aggregator($aggregateFunction);

        //$database = $this->getSmallPeopleDatabase();
        $database  = $this->coordinator->getDatabase('people');
        $rawResult = $this->fixture->perform($database);
        $result    = array();
        foreach ($rawResult as $propertyKey => $typesAndCount) {
            $result[] = new Description($propertyKey, array_keys($typesAndCount['types']), $typesAndCount['count']);
        }
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
        $aggregateFunction = function ($document) use (&$mapInvocationCounter) {
            ++$mapInvocationCounter;
        };

        $this->fixture = new Aggregator($aggregateFunction);

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
        $aggregateFunction = function ($document) use (&$mapInvocationCounter) {
            ++$mapInvocationCounter;
        };

        $this->fixture = new Aggregator($aggregateFunction);

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
     * @expectedException \Cundd\PersistentObjectStore\Exception\InvalidCollectionException
     */
    public function invalidNullInputTest()
    {
        $this->fixture->perform(null);
    }

    /**
     * @test
     * @expectedException \Cundd\PersistentObjectStore\Exception\InvalidCollectionException
     */
    public function invalidStringInputTest()
    {
        $this->fixture->perform('anything');
    }
}
