<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 28.02.15
 * Time: 14:41
 */

namespace Cundd\PersistentObjectStore\MapReduce;


use Cundd\PersistentObjectStore\AbstractDatabaseBasedCase;
use Cundd\PersistentObjectStore\Constants;
use Cundd\PersistentObjectStore\Domain\Model\DocumentInterface;
use Cundd\PersistentObjectStore\Meta\Database\Property\Description;
use Cundd\PersistentObjectStore\Utility\GeneralUtility;

class CoordinatorTest extends AbstractDatabaseBasedCase
{
    /**
     * @var \Cundd\PersistentObjectStore\MapReduce\Coordinator
     */
    protected $fixture;

    protected function setUp()
    {
    }


    /**
     * @test
     */
    public function mapReduceTest()
    {
        /**
         * @param DocumentInterface $document
         */
        $mapFunction = function ($document) {
            $allProperties = array_keys($document->getData());
            foreach ($allProperties as $propertyKey) {
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


        $this->fixture = new Coordinator($mapFunction, $reduceFunction);
        $result        = $this->fixture->perform($this->getSmallPeopleDatabase());

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
    public function mapReducePropertyDescriptionTest()
    {
        /**
         * @param DocumentInterface $document
         */
        $mapFunction = function ($document) {
            $allProperties = $document->getData();
            foreach ($allProperties as $propertyKey => $propertyValue) {
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
         * @return number
         */
        $reduceFunction = function ($key, $values) {
            $types = array();
            $count = 0;

            foreach ($values as $valueBlock) {
                $types[] = $valueBlock['type'];
                $count += $valueBlock['count'];
            }
            return new Description($key, $types, $count);
        };


        $this->fixture = new Coordinator($mapFunction, $reduceFunction);
        $result        = $this->fixture->perform($this->getSmallPeopleDatabase());

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
}
