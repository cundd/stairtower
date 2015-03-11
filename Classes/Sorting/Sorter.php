<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 30.08.14
 * Time: 14:34
 */

namespace Cundd\PersistentObjectStore\Sorting;

use Cundd\PersistentObjectStore\Domain\Model\Database;
use Cundd\PersistentObjectStore\KeyValueCodingInterface;
use Cundd\PersistentObjectStore\Sorting\Exception\SortingException;
use Cundd\PersistentObjectStore\Utility\ObjectUtility;
use SplFixedArray;

/**
 * Class to sort a collection of objects
 *
 * @package Cundd\PersistentObjectStore\Sorting
 */
class Sorter
{
    /**
     * Sorting flags
     *
     * @see http://php.net/manual/de/function.sort.php
     * @var int
     */
    protected $sortFlags = SORT_REGULAR;

    /**
     * Returns the sorting flags
     *
     * @see http://php.net/manual/de/function.sort.php
     * @return int
     */
    public function getSortFlags()
    {
        return $this->sortFlags;
    }

    /**
     * Sets the sorting flags
     *
     * @see http://php.net/manual/de/function.sort.php
     * @param int $sortFlags
     * @return $this
     */
    public function setSortFlags($sortFlags)
    {
        $this->sortFlags = $sortFlags;
        return $this;
    }

    /**
     * Sort the collection of objects by the given key
     *
     * @param Database|\Iterator|array $collection
     * @param string                   $keyPath
     * @param bool                     $descending
     * @return SortResult
     */
    public function sortCollectionByPropertyKeyPath($collection, $keyPath, $descending = false)
    {
        $packedObjectsKey = 'objects';
        $resultArray      = array();

        if (is_array($collection)) {
            $dataCollectionRaw = SplFixedArray::fromArray($collection);
        } elseif ($collection instanceof SplFixedArray) {
            $dataCollectionRaw = $collection;
        } elseif ($collection instanceof Database) {
            $dataCollectionRaw = $collection->toFixedArray();
        } else {
            $dataCollectionRaw = SplFixedArray::fromArray(iterator_to_array($collection));
        }
        $dataCollectionCount = $dataCollectionRaw->getSize();


        // Pack the objects grouped by the property value
        $i = 0;
        while ($i < $dataCollectionCount) {
            $item = $dataCollectionRaw[$i];

            // Fetch to property value
            if ($item instanceof KeyValueCodingInterface) {
                $propertyValue = $item->valueForKeyPath($keyPath);
            } else {
                $propertyValue = ObjectUtility::valueForKeyPathOfObject($keyPath, $item);
            }

            // Prepare the packed result array
            if (is_float($propertyValue)) {
                $propertyValue .= '';
            } elseif ($propertyValue !== null && !is_scalar($propertyValue)) {
                throw new SortingException(
                    sprintf(
                        'Could not sort by property key path %s, because one value is of type %s',
                        $keyPath,
                        gettype($propertyValue)
                    ),
                    1412021636
                );
            }
            if (!isset($resultArray[$propertyValue])) {
                $resultArray[$propertyValue] = array(
                    $packedObjectsKey => array()
                );
            }
            $resultArray[$propertyValue][$packedObjectsKey][] = $item;

            $i++;
        }

        // Sort the objects
        if (!$descending) {
            $result = ksort($resultArray, $this->sortFlags);
        } else {
            $result = krsort($resultArray, $this->sortFlags);
        }
        if (!$result) {
            throw new SortingException('Could not sort the database', 1412021636);
        }

        // Unpack the objects
        $i                = 0;
        $j                = 0;
        $resultFixedArray = new SortResult($dataCollectionCount);
        $resultArray      = SplFixedArray::fromArray(array_values($resultArray));
        $resultArrayCount = $resultArray->count();
        while ($i < $resultArrayCount) {
            $packedObjects = $resultArray[$i][$packedObjectsKey];
            $item          = current($packedObjects);
            do {
                $resultFixedArray[$j] = $item;
                $j++;
            } while ($item = next($packedObjects));
            $i++;
        }

        if ($j != $dataCollectionCount) {
            throw new SortingException(sprintf('Number of result items does not match the number of input items (%d/%d)',
                $j, $dataCollectionCount), 1412243235);
        }
        return $resultFixedArray;
    }

    /**
     * Sort the collection of objects by invoking the given callback
     *
     * @param Database|\Iterator|array $collection
     * @param callback                 $callback
     * @param bool                     $descending
     * @return SortResult
     */
    public function sortCollectionByCallback($collection, $callback, $descending = false)
    {
        if (is_array($collection)) {
            $dataCollection = SplFixedArray::fromArray($collection);
        } elseif ($collection instanceof Database) {
            $dataCollection = $collection->toFixedArray();
        } else {
            $dataCollection = SplFixedArray::fromArray(iterator_to_array($collection));
        }

        $dataCollectionRaw = $dataCollection->toArray();
        if (!uasort($dataCollectionRaw, $callback)) {
            throw new SortingException('Could not sort the database', 1412021637);
        }

        if ($descending) {
            array_reverse($dataCollectionRaw);
        }
        return SortResult::fromArray(array_values($dataCollectionRaw));
    }
}
