<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Index;

/**
 * Interface for classes that have associated Indexes
 */
interface IndexableInterface
{
    /**
     * Queries the registered Indexes for the given value and property
     *
     * If one of the associated Indexes returned a lookup result the matching data will be returned.
     * If one of the associated Indexes returned IndexInterface::NOT_FOUND the NOT_FOUND constant will be returned.
     * If none of the Indexes could return a valid result IndexInterface::NO_RESULT will be returned.
     *
     * @param mixed  $value
     * @param string $property
     * @return mixed Returns the found data, IndexInterface::NOT_FOUND or IndexInterface::NO_RESULT
     */
    public function queryIndexesForValueOfProperty($value, $property);

    /**
     * Returns if the object has an Index that can handle the given property key and value
     *
     * @param mixed  $value
     * @param string $property
     * @return bool
     */
    public function hasIndexesForValueOfProperty($value, $property);

    /**
     * Returns the object's Indexes that can handle the given property key and value
     *
     * @param mixed  $value
     * @param string $property
     * @return IndexInterface[]
     */
    public function getIndexesForValueOfProperty($value, $property);
}