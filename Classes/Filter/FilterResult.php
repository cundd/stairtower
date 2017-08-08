<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Filter;

use Cundd\PersistentObjectStore\Core\ArrayException\IndexOutOfRangeException;
use Cundd\PersistentObjectStore\Core\IndexArray;
use Cundd\PersistentObjectStore\Domain\Model\Database;
use Cundd\PersistentObjectStore\Domain\Model\DatabaseInterface;
use Cundd\PersistentObjectStore\Domain\Model\DatabaseObjectDataInterface;
use Cundd\PersistentObjectStore\Domain\Model\DatabaseRawDataInterface;
use Cundd\PersistentObjectStore\Exception\ImmutableException;
use Cundd\PersistentObjectStore\Filter\Comparison\ComparisonInterface;
use Cundd\PersistentObjectStore\Filter\Comparison\PropertyComparisonInterface;
use Cundd\PersistentObjectStore\Immutable;
use Cundd\PersistentObjectStore\Index\IndexableInterface;
use Cundd\PersistentObjectStore\Index\IndexInterface;
use Iterator;
use SplFixedArray;

/**
 * Result of a filtered collection
 */
class FilterResult extends IndexArray implements FilterResultInterface, Immutable
{
    /**
     * Collection to filter
     *
     * @var Database|Iterator
     */
    protected $collection;

    /**
     * Filter to apply to the collection
     *
     * @var FilterInterface
     */
    protected $filter;

    /**
     * Defines if the collection has been filtered once
     *
     * @var bool
     */
    protected $fullyFiltered = false;


    /**
     * @param Database|Iterator $originalCollection
     * @param FilterInterface   $filter
     */
    public function __construct($originalCollection, $filter)
    {
        parent::__construct(null);
        $this->collection = $this->cloneCollection($originalCollection);
        $this->filter = $filter;
    }

    /**
     * Creates a deep clone of the given collection
     *
     * @param Database|Iterator $originalCollection
     * @return \Traversable
     */
    protected function cloneCollection($originalCollection)
    {
        // If the collection is immutable just return it
        if ($originalCollection instanceof Immutable) {
            return $originalCollection;
        }
        if ($originalCollection instanceof DatabaseInterface) {
            return clone $originalCollection;
        }

        $i = 0;
        $count = $originalCollection->count();
        if ($count === 0) {
            return new SplFixedArray();
        }
        $collection = new SplFixedArray($originalCollection->count());

        // Separate handling to loop over SplFixedArray
        if ($originalCollection instanceof SplFixedArray) {
            do {
                $collection[$i] = clone $originalCollection[$i];
            } while (++$i < $count);
        } else {
            foreach ($originalCollection as $item) {
                $collection[$i] = clone $item;
                $i++;
            }
        }
        $collection->rewind();

        return $collection;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     *
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        $this->initFilteredCollection();
        if ($this->currentIndex >= $this->length) {
            if ($this->length === 0) {
                throw new IndexOutOfRangeException('Filter has an empty result', 1420043991);
            }
            throw new IndexOutOfRangeException(
                sprintf(
                    'Current Filter Result index %d is out of range',
                    $this->currentIndex
                ),
                1420043992
            );
        }

        return parent::current();
    }

    /**
     * Initializes the filtered collection
     */
    protected function initFilteredCollection()
    {
        if ($this->length === 0) {
            $this->findNext();
        }
    }

    /**
     * Find the next matching object
     *
     * Returns NULL if none was found
     *
     * @return mixed
     */
    protected function findNext()
    {
        // If the filtered collection is fully populated
        if ($this->fullyFiltered) {
            if (!parent::valid()) {
                return null;
            }

            return parent::current();
        }
        $foundObject = null;
        $collection = null;
        if ($this->collection instanceof IndexableInterface) {
            /** @var ComparisonInterface $comparison */
            $comparison = $this->filter->getComparison();
            $collection = $this->queryIndexesForComparison($this->collection, $comparison);
        }
        if ($collection === null) {
            //$collection = $this->collectionToFixedArray($this->collection);
            $collection = $this->collection;
        }

        $filter = $this->filter;
        $useRaw = method_exists($collection, 'currentRaw');

        // Loop through the collection until one matching object was found
        while ($collection->valid()) {
            if ($useRaw) {
                $item = $collection->currentRaw();
            } else {
                $item = $collection->current();
            }
            if ($filter->checkItem($item)) {
                if ($useRaw) {
                    $foundObject = $collection->current();
                } else {
                    $foundObject = $item;
                }
                $collection->next();
                break;
            }
            $collection->next();
        }

        // We reached the end
        if (!$collection->valid()) {
            $this->fullyFiltered = true;
        }

        if ($foundObject) {
            parent::push($foundObject);
        }

        return $foundObject;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     *
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        $this->initFilteredCollection();

        $this->findNext();
        $this->currentIndex++;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     *
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        $this->initFilteredCollection();

        return parent::key();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     *
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     *       Returns true on success or false on failure.
     */
    public function valid()
    {
//		DebugUtility::var_dump('valid', parent::valid(), parent::valid(), parent::valid());
        $this->initFilteredCollection();

        return parent::valid();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     *
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->currentIndex = 0;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     *
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     *       </p>
     *       <p>
     *       The return value is cast to an integer.
     */
    public function count()
    {
        if (!$this->fullyFiltered) {
            $this->findAll();
        }

        return parent::count();
    }

    /**
     * Finds all matching object by choosing the right implementation for Databases or regular collections
     */
    protected function findAll()
    {
        $lastIndex = $this->currentIndex;
        $this->filterCollectionWithComparison($this->collection, $this->filter->getComparison(), true);
        $this->fullyFiltered = true;
        $this->currentIndex = $lastIndex;
    }

    /**
     * Filter the given Database by the given comparison
     *
     * @param Database|\Iterator  $dataCollection      Database instance to filter
     * @param ComparisonInterface $comparison          Filter condition
     * @param bool                $pushMatchesToResult If set to TRUE the matching objects will be added to the result through calling parent::push()
     * @return \Traversable
     */
    protected function filterCollectionWithComparison(
        $dataCollection,
        $comparison,
        $pushMatchesToResult = false
    ) {
        if (!$comparison) {
            return $dataCollection;
        }

        // Get the collection data as SplFixedArray
        $callMethodGetObjectDataForIndex = false;
        if ($dataCollection instanceof DatabaseObjectDataInterface) {
            $callMethodGetObjectDataForIndex = true;
        }

        $fixedDataCollection = null;
        if ($dataCollection instanceof IndexableInterface) {
            $fixedDataCollection = $this->queryIndexesForComparison($dataCollection, $comparison);
        }
        if ($fixedDataCollection === null) {
            $fixedDataCollection = $this->collectionToFixedArray($dataCollection);
        }
        $dataCollectionCount = $fixedDataCollection->getSize();


        $resultArray = new SplFixedArray($dataCollectionCount);

        $i = 0;
        $matchesIndex = 0;
        while ($i < $dataCollectionCount) {
            $item = $fixedDataCollection[$i];

            if ($comparison->perform($item)) {
                if ($callMethodGetObjectDataForIndex) {
                    /** @var DatabaseObjectDataInterface $dataCollection */
                    $item = $dataCollection->getObjectDataForIndex($i);
                }
                // Todo: Check for null values
                $resultArray[$matchesIndex] = $item;

                if ($pushMatchesToResult) {
                    parent::offsetSet($matchesIndex, $item);
                }
                $matchesIndex++;
            }
            $i++;
        }
        $resultArray->setSize($matchesIndex);

        return SplFixedArray::fromArray($resultArray->toArray());
    }

    /**
     * Returns the filtered items as fixed array
     *
     * @return SplFixedArray
     */
    public function toFixedArray(): \SplFixedArray
    {
        return SplFixedArray::fromArray($this->toArray());
    }

    /**
     * Returns the filtered items as array
     *
     * @return array
     */
    public function toArray(): array
    {
        if (!$this->fullyFiltered) {
            $this->findAll();
        }

        return $this->elements;
    }

    /**
     * Adds an element to the end of the array
     *
     * @param mixed $value
     * @throws \Cundd\PersistentObjectStore\Exception\ImmutableException
     */
    public function push($value)
    {
        throw new ImmutableException('Can not modify this immutable', 1410628420);
    }

    /**
     * Pops the element from the end of the array
     *
     * @throws \Cundd\PersistentObjectStore\Exception\ImmutableException
     * @return mixed
     */
    public function pop()
    {
        throw new ImmutableException('Can not modify this immutable', 1410628420);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     *                      The offset to assign the value to.
     *                      </p>
     * @param mixed $value  <p>
     *                      The value to set.
     *                      </p>
     * @throws \Cundd\PersistentObjectStore\Exception\ImmutableException
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        throw new ImmutableException('Can not modify this immutable', 1410628420);

    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     *                      The offset to unset.
     *                      </p>
     * @throws \Cundd\PersistentObjectStore\Exception\ImmutableException
     * @return void
     */
    public function offsetUnset($offset)
    {
        throw new ImmutableException('Can not modify this immutable', 1410628420);
    }

    /**
     * Returns the matching elements for the given comparison queried from the collection's Indexes or the full
     * collection (as SplFixedArray) if no Index is handling the comparison
     *
     * @param IndexableInterface  $collection
     * @param ComparisonInterface $comparison
     * @return SplFixedArray
     */
    protected function queryIndexesForComparison(IndexableInterface $collection, ComparisonInterface $comparison)
    {
        $foundObjects = null;
        if ($comparison instanceof PropertyComparisonInterface) {
            $comparisonValue = $comparison->getValue();
            $comparisonProperty = $comparison->getProperty();

            if ($comparison->getOperator() === ComparisonInterface::TYPE_EQUAL_TO
                && $collection->hasIndexesForValueOfProperty($comparisonValue, $comparisonProperty)
            ) {
                $indexQueryResult = $collection->queryIndexesForValueOfProperty($comparisonValue, $comparisonProperty);
                if ($indexQueryResult <= IndexInterface::NO_RESULT) {
                    $foundObjects = null;
                } elseif ($indexQueryResult === IndexInterface::NOT_FOUND) {
                    $foundObjects = new SplFixedArray(0);
                } else {
                    $foundObjects = SplFixedArray::fromArray($indexQueryResult);
                }
            }
        }

        return $foundObjects;
    }

    /**
     * Transforms the given collection into a SplFixedArray
     *
     * @param DatabaseRawDataInterface|Iterator|array $collection
     * @return SplFixedArray
     */
    protected function collectionToFixedArray($collection)
    {
        // Get the collection data as SplFixedArray and return it
        if ($collection instanceof DatabaseRawDataInterface) {
            $fixedCollection = $collection->getRawData();
        } elseif (is_array($collection)) {
            $fixedCollection = SplFixedArray::fromArray($collection);
        } elseif ($collection instanceof Iterator) {
            $collection->rewind();
            $fixedCollection = SplFixedArray::fromArray(iterator_to_array($collection));
        } else {
            $fixedCollection = new SplFixedArray(0);
        }

        return $fixedCollection;
    }

    /**
     * Apply the first comparison to the given collection
     *
     * @param Database|Iterator $collection
     * @return SplFixedArray|Iterator
     */
    protected function preFilterCollection($collection)
    {
        return $collection;
    }
} 