<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 25.02.15
 * Time: 21:08
 */

namespace Cundd\PersistentObjectStore\Meta\Database\Property;


use Cundd\PersistentObjectStore\Domain\Model\DatabaseInterface;
use Cundd\PersistentObjectStore\Domain\Model\DatabaseRawDataInterface;
use Cundd\PersistentObjectStore\Domain\Model\DocumentInterface;
use Cundd\PersistentObjectStore\MapReduce\MapReduce;
use Cundd\PersistentObjectStore\MapReduce\MapReduceInterface;
use Cundd\PersistentObjectStore\Meta\DescriptorInterface;
use Cundd\PersistentObjectStore\Meta\Exception\DescriptorSubjectException;
use Cundd\PersistentObjectStore\Utility\GeneralUtility;

/**
 * Descriptor for properties in a Database
 *
 * @package Cundd\PersistentObjectStore\Meta\Database
 */
class Descriptor implements DescriptorInterface
{
    /**
     * @var MapReduceInterface
     */
    protected $mapReduce;

    /**
     * Returns the description of the subject
     *
     * @param DatabaseInterface $subject
     * @return mixed
     */
    public function describe($subject)
    {
        if (!$subject instanceof DatabaseInterface) {
            throw DescriptorSubjectException::descriptorException(
                'Cundd\\PersistentObjectStore\\Domain\\Model\\DatabaseInterface', $subject, 1424896728
            );
        }
        $collection = $subject;
        if ($subject instanceof DatabaseRawDataInterface) {
            $collection = $subject->getRawData();
        }
        return $this->getMapReduce()->perform($collection);
    }

    /**
     * Returns the MapReduce instance
     *
     * @return MapReduceInterface
     */
    protected function getMapReduce()
    {
        if (!$this->mapReduce) {
            /**
             * @param DocumentInterface|array $document
             */
            $mapFunction = function ($document) {
                if (is_array($document)) {
                    $allProperties = $document;
                } else {
                    $allProperties = $document->getData();
                }
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

            $this->mapReduce = new MapReduce($mapFunction, $reduceFunction);
        }
        return $this->mapReduce;
    }
}