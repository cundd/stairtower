<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Index;

/**
 * Trait for Indexable
 */
trait IndexableTrait
{
    /**
     * Collection of Indexes
     *
     * @var IndexInterface[]
     */
    protected $indexes = [];

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
    public function queryIndexesForValueOfProperty($value, $property)
    {
        if (!$this->indexes) {
            return IndexInterface::NO_RESULT;
        }

        // Loop through each of the Indexes
        $i = 0;
        $indexesCount = count($this->indexes);

        do {
            $indexInstance = $this->indexes[$i];
            // If the Index can look up the given value and the Index manages the ID property take the results from it
            if ($indexInstance->getProperty() === $property && $indexInstance->canLookup($value)) {
                $indexLookupResult = $indexInstance->lookup($value);
                if ($indexLookupResult <= IndexInterface::NO_RESULT) {
                    continue;
                }
                if ($indexLookupResult === IndexInterface::NOT_FOUND) {
                    return IndexInterface::NOT_FOUND;
                }
                $resultCollection = [];
                foreach ($indexLookupResult as $currentIndexLookupResult) {
                    $resultCollection[] = $this->getObjectDataForIndex($currentIndexLookupResult);
                }

                return $resultCollection;
            }
        } while (++$i < $indexesCount);

        return IndexInterface::NO_RESULT;
    }

    /**
     * Returns if the object has an Index that can handle the given property key and value
     *
     * @param mixed  $value
     * @param string $property
     * @return bool
     */
    public function hasIndexesForValueOfProperty($value, $property)
    {
        return count($this->getIndexesForValueOfProperty($value, $property)) > 0;
    }

    /**
     * Returns the object's Indexes that can handle the given property key and value
     *
     * @param mixed  $value
     * @param string $property
     * @return \Cundd\Stairtower\Index\IndexInterface[]
     */
    public function getIndexesForValueOfProperty($value, $property)
    {
        $i = 0;
        $matchingIndexes = [];
        $indexesCount = count($this->indexes);
        do {
            $indexInstance = $this->indexes[$i];
            // If the Index can look up the given value and the Index manages the ID property add it to the result
            if ($indexInstance->getProperty() === $property && $indexInstance->canLookup($value)) {
                $matchingIndexes[] = $indexInstance;
            }
        } while (++$i < $indexesCount);

        return $matchingIndexes;
    }

    /**
     * Returns the Document instance at the given index or sets it if it is not already set
     *
     * @param int $index
     * @return bool|object
     */
    abstract public function getObjectDataForIndex($index);
}