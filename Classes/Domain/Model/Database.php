<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 25.08.14
 * Time: 21:30
 */

namespace Cundd\PersistentObjectStore\Domain\Model;

use Cundd\PersistentObjectStore\ArrayableInterface;
use Cundd\PersistentObjectStore\Constants;
use Cundd\PersistentObjectStore\Core\ArrayException\IndexOutOfRangeException;
use Cundd\PersistentObjectStore\Core\ArrayException\InvalidIndexException;
use Cundd\PersistentObjectStore\DataAccess\Event;
use Cundd\PersistentObjectStore\Domain\Model\Exception\DatabaseMismatchException;
use Cundd\PersistentObjectStore\Domain\Model\Exception\InvalidDataException;
use Cundd\PersistentObjectStore\Event\SharedEventEmitter;
use Cundd\PersistentObjectStore\Filter\Comparison\ComparisonInterface;
use Cundd\PersistentObjectStore\Filter\Exception\InvalidCollectionException;
use Cundd\PersistentObjectStore\Filter\Filter;
use Cundd\PersistentObjectStore\Index\IdentifierIndex;
use Cundd\PersistentObjectStore\Index\IndexInterface;
use Cundd\PersistentObjectStore\RuntimeException;
use Cundd\PersistentObjectStore\Utility\DebugUtility;
use Cundd\PersistentObjectStore\Utility\DocumentUtility;
use Cundd\PersistentObjectStore\Utility\GeneralUtility;
use SplFixedArray;

/**
 * Database class which holds the Document instances
 *
 * Implementation with object creation on demand.
 *
 * @package Cundd\PersistentObjectStore\Domain\Model
 */
class Database implements DatabaseInterface, ArrayableInterface
{
    /**
     * Raw data array
     *
     * @var \SplFixedArray
     */
    protected $rawData;

//	/**
//	 * Map of object identifiers to the index
//	 *
//	 * @var array
//	 */
//	protected $idToIndexMap = array();

    /**
     * Database identifier
     *
     * @var string
     */
    protected $identifier = '';

    /**
     * Current index
     *
     * @var int
     */
    protected $index = 0;

    /**
     * Database's current state
     *
     * @var string
     */
    protected $state = self::STATE_CLEAN;

    /**
     * Collection of Indexes
     *
     * @var IndexInterface[]
     */
    protected $indexes = array();

    /**
     * Creates a new database
     *
     * @param string $identifier
     * @param array  $rawData
     */
    public function __construct($identifier, $rawData = array())
    {
        GeneralUtility::assertDatabaseIdentifier($identifier);
        $this->identifier = $identifier;

        if ($rawData) {
            $this->setRawData($rawData);
        } else {
            $this->rawData    = new SplFixedArray(0);
        }

        $this->indexes[] = new IdentifierIndex();
    }

    /**
     * Returns the database identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Returns the raw data
     *
     * @return \SplFixedArray
     * @internal
     */
    public function getRawData()
    {
        if (!$this->rawData) {
            $this->rawData = new \SplFixedArray(0);
        }
        return $this->rawData;
    }

    /**
     * Sets the raw data
     *
     * @param \SplFixedArray|array|\Iterator $rawData
     */
    public function setRawData($rawData)
    {
        if ($rawData instanceof SplFixedArray) {
            // Use the fixed array as is
        } elseif (is_array($rawData)) {
            $rawData = SplFixedArray::fromArray($rawData);
        } elseif ($rawData instanceof \Iterator) {
            $rawData = SplFixedArray::fromArray(iterator_to_array($rawData));
        } else {
            throw new InvalidCollectionException('Could not set raw data', 1412017652);
        }

        // Make sure all raw Documents have an ID
        $i     = 0;
        $count = $rawData->getSize();
        if ($count > 0) {
            $tempRawData = new SplFixedArray($count);
            do {
                $tempRawData[$i] = DocumentUtility::assertDocumentIdentifierOfData($rawData[$i]);
            } while (++$i < $count);
            $this->rawData = $tempRawData;
        } else {
            $this->rawData = new SplFixedArray(0);
        }

        $this->state = self::STATE_DIRTY;

        $this->rebuildIndexes();
    }

    /**
     * Returns the current raw data
     *
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        $document = $this->getRawDataForIndex($this->index);
        if ($document === false) {
            throw new IndexOutOfRangeException('Invalid index ' . $this->index, 1411316363);
        }
        return $document;
    }

    /**
     * Returns the raw data at the given index or FALSE if it is not set
     *
     * @param int $index
     * @return bool|mixed
     */
    public function getRawDataForIndex($index)
    {
        if (isset($this->rawData[$index])) {
            $data = $this->rawData[$index];
            return DocumentUtility::assertDocumentIdentifierOfData($data);
        }
        return false;
    }

    // MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
    // STATE
    // MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
    /**
     * Returns the Database's current state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Sets the Database's state
     *
     * @param string $newState
     * @return $this
     */
    public function setState($newState)
    {
        $this->state = $newState;
        return $this;
    }

    // MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
    // MANAGING OBJECTS
    // MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW

    /**
     * Filters the database using the given comparisons
     *
     * @param array|ComparisonInterface $comparisons
     * @return \Cundd\PersistentObjectStore\Filter\FilterResultInterface
     */
    public function filter($comparisons)
    {
        if (!is_array($comparisons)) {
            $comparisons = func_get_args();
        }
        $filter = new Filter($comparisons);
        return $filter->filterCollection($this);
    }

    /**
     * Adds the given Document to the database
     *
     * @param DocumentInterface $document
     */
    public function add($document)
    {
        $this->assertDataInstancesDatabaseIdentifier($document);
        DocumentUtility::assertDocumentIdentifier($document);
        $currentCount = $this->count();

        if ($this->contains($document)) {
            throw new InvalidDataException(
                sprintf('Object with GUID %s already exists in the database. Maybe the values of the identifier is not expressive',
                    $document->getGuid()),
                1411205350
            );
        }


        $this->rawData->setSize($currentCount + 1);
        $this->setRawDataForIndex($document->getData(), $currentCount);

        $this->addToIndexAtPosition($document, $currentCount);

        $this->state = self::STATE_DIRTY;
        SharedEventEmitter::emit(Event::DATABASE_DOCUMENT_ADDED, array($document));
    }

    /**
     * Checks if the Document instance's database identifier is correct
     *
     * @param DocumentInterface $document
     */
    protected function assertDataInstancesDatabaseIdentifier($document)
    {
        if (!is_object($document)) {
            throw new InvalidDataException(sprintf('Given data instance is not of type object but %s',
                gettype($document)), 1412859398);
        }
        $databaseIdentifier = $document->getDatabaseIdentifier();
        if (!$databaseIdentifier) {
            if ($document instanceof Document) {
                $document->setDatabaseIdentifier($this->identifier);
            }
        } else {
            if ($databaseIdentifier !== $this->identifier) {
                throw new DatabaseMismatchException(
                    sprintf(
                        'The given Document instance does not belong to this database (Document instance database identifier: %s, Database identifier: %s',
                        $databaseIdentifier,
                        $this->identifier
                    ),
                    1411315947
                );
            }
        }
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
        return $this->rawData->count();
    }

    /**
     * Returns if the database contains the given Document
     *
     * @param DocumentInterface|string $document Actual Document instance or it's GUID
     * @return boolean
     */
    public function contains($document)
    {
        if (is_string($document)) {
            $identifier = $document;
        } elseif ($document instanceof DocumentInterface) {
            $this->assertDataInstancesDatabaseIdentifier($document);
            DocumentUtility::assertDocumentIdentifier($document);
            $identifier = $document->getId();
        } else {
            throw new RuntimeException("Given value $document is of type " . gettype($document));
        }
        return $this->findByIdentifier($identifier) ? true : false;
    }



    // MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
    // MANAGING INDEXES
    // MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW

    /**
     * Returns the object with the given identifier
     *
     * @param string $identifier
     * @return DocumentInterface|NULL
     */
    public function findByIdentifier($identifier)
    {
        // Query the Indexes and return the result if it is not an error
        $indexLookupResult = $this->queryIndexesForValueOfProperty($identifier, Constants::DATA_ID_KEY);
        if ($indexLookupResult !== IndexInterface::ERROR) {
            if ($indexLookupResult === IndexInterface::NOT_FOUND) {
                return null;
            }
            return $indexLookupResult[0];
        }

        $i     = 0;
        $count = $this->count();
        if ($count === 0) {
            return null;
        }

        do {
//			DebugUtility::pl($i);
            if (isset($this->rawData[$i]) && $this->rawData[$i] && $this->rawData[$i][Constants::DATA_ID_KEY]) {
                return $this->rawData[$i];
            }
        } while (++$i < $count);

//		$i = 0;
//		do {
////			DebugUtility::pl($i);
//			if (isset($this->objectData[$i])) {
//				$foundObject = $this->objectData[$i];
//			} else {
//				$foundObject = $this->_setObjectDataForIndex($this->_convertDataAtIndexToObject($i), $i);
//			}
//
//			if ($foundObject instanceof DocumentInterface && $foundObject->getId() === $identifier) {
//				return $foundObject;
//			}
//		} while (++$i < $count);
        return null;
    }

    /**
     * Queries the registered Indexes for the given value and property
     *
     * @param mixed  $value
     * @param string $property
     * @return bool|DocumentInterface[] Returns the Documents if found in one of the Indexes, IndexInterface::NOT_FOUND or IndexInterface::ERROR if an error occurred
     */
    protected function queryIndexesForValueOfProperty($value, $property)
    {
        if (!$this->indexes) {
            return IndexInterface::ERROR;
        }

        // Loop through each of the Indexes
        $i            = 0;
        $indexesCount = count($this->indexes);
        do {
            $indexInstance = $this->indexes[$i];
            // If the Index can look up the given value and the Index manages the ID property take the resulte from it
            if ($indexInstance->getProperty() === $property && $indexInstance->canLookup($value)) {
                $indexLookupResult = $indexInstance->lookup($value);
                if ($indexLookupResult === IndexInterface::ERROR) {
                    continue;
                }
                if ($indexLookupResult === IndexInterface::NOT_FOUND) {
                    return IndexInterface::NOT_FOUND;
                }
//				DebugUtility::pl('Hit index %s', get_class($indexInstance));
//				DebugUtility::var_dump($indexLookupResult);
//                return $this->getObjectDataForIndexOrTransformIfNotExists($indexLookupResult);
                $resultCollection = array();
                foreach ($indexLookupResult as $currentIndexLookupResult) {
                    $resultCollection[] = $this->getRawDataForIndex($currentIndexLookupResult);
                }
                return $resultCollection;
            }
        } while (++$i < $indexesCount);
        return IndexInterface::ERROR;
    }



    // MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
    // ITERATOR, COUNTABLE AND SEEKABLE
    // MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW


    /**
     * Converts the raw data at the given index to a Document instance
     *
     * @param integer $index
     * @return DocumentInterface
     */
    protected function convertDataAtIndexToObject($index)
    {
        if (isset($this->rawData[$index]) && $this->rawData[$index] === null) {
            return null;
        }
        if (!isset($this->rawData[$index])) {
            DebugUtility::var_dump(
                __METHOD__ . ' valid',
                $this->index < $this->count() || isset($this->rawData[$this->index]),
                $this->index < $this->count(),
                isset($this->rawData[$this->index])
            );
            DebugUtility::var_dump(
                $index,
                $index < $this->count() || isset($this->rawData[$index]),
                $index < $this->count(),
                isset($this->rawData[$index])
            );
            DebugUtility::var_dump($this->rawData);
            throw new IndexOutOfRangeException('Invalid index ' . $index, 1411316363);

        }
//		if (!isset($this->rawData[$index])) throw new IndexOutOfRangeException('Invalid index ' . $index);
        $rawData    = $this->rawData[$index];
        $rawData    = DocumentUtility::assertDocumentIdentifierOfData($rawData);
        $dataObject = new Document($rawData, $this->identifier);

//		if (isset($rawMetaData['creation_time'])) {
//			$dataObject->setCreationTime($rawMetaData['creation_time']);
//		}
//		if (isset($rawMetaData['modification_time'])) {
//			$dataObject->setModificationTime($rawMetaData['modification_time']);
//		}

        return $dataObject;
    }

    /**
     * Sets the raw data at the given index
     *
     * @param mixed $data
     * @param int   $index
     *
     * @return mixed Returns the given data
     */
    protected function setRawDataForIndex($data, $index)
    {
        $this->rawData[$index] = DocumentUtility::assertDocumentIdentifierOfData($data);
        return $data;
    }

    /**
     * Adds the given data instance to the Indexes
     *
     * @param DocumentInterface $document
     * @param int               $position
     */
    protected function addToIndexAtPosition($document, $position)
    {
        foreach ($this->indexes as $indexInstance) {
            $indexInstance->addEntryWithPosition($document, $position);
        }
    }

    /**
     * Updates the given Document in the database
     *
     * @param DocumentInterface $document
     */
    public function update($document)
    {
        $this->assertDataInstancesDatabaseIdentifier($document);
        DocumentUtility::assertDocumentIdentifier($document);

        if (!$this->contains($document)) {
            throw new InvalidDataException(
                sprintf('Object with GUID %s does not exist in the database. Maybe the values of the identifier is not expressive',
                    $document->getGuid()),
                1412800596
            );
        }

        $index           = $this->getIndexForIdentifier($document->getId());


        $this->setRawDataForIndex($document->getData(), $index);

        $this->updateIndexForPosition($document, $index);

        SharedEventEmitter::emit(Event::DATABASE_DOCUMENT_UPDATED, array($document));
    }

    /**
     * Returns the index for the given identifier or -1 if it does not exist
     *
     * @param string $identifier
     * @return int
     */
    protected function getIndexForIdentifier($identifier)
    {
        $count         = $this->count();
        $i             = 0;
        $matchingIndex = -1;

        do {
            $rawData = $this->setRawDataIdentifierIfNotSetForIndex($i);
            if ($rawData[Constants::DATA_ID_KEY] === $identifier) {
                $matchingIndex = $i;
                break;
            }
        } while (++$i < $count);
        return $matchingIndex;
    }

    /**
     * Checks if the raw data's identifier is defined
     *
     * @param int $index
     * @return array Returns the prepared data
     */
    protected function setRawDataIdentifierIfNotSetForIndex($index)
    {
        $rawData = $this->rawData[$index];
        if (!isset($rawData[Constants::DATA_ID_KEY]) || $rawData[Constants::DATA_ID_KEY]) {
            $this->rawData[$index] = DocumentUtility::assertDocumentIdentifierOfData($rawData);
        }
        return $rawData;
    }

    /**
     * Updates the given data instance in the Indexes
     *
     * @param DocumentInterface $document
     * @param int               $position
     */
    protected function updateIndexForPosition($document, $position)
    {
        foreach ($this->indexes as $indexInstance) {
            $indexInstance->updateEntryForPosition($document, $position);
        }
    }

    /**
     * Removes the given Document from the database
     *
     * @param DocumentInterface $document
     */
    public function remove($document)
    {
        $this->assertDataInstancesDatabaseIdentifier($document);
        DocumentUtility::assertDocumentIdentifier($document);

        if (!$this->contains($document)) {
            throw new InvalidDataException(
                sprintf('Object with GUID %s does not exist in the database. Maybe the values of the identifier is not expressive',
                    $document->getGuid()),
                1412800595
            );
        }

        $index = $this->getIndexForIdentifier($document->getId());
        if ($index === -1) {
            throw new RuntimeException(
                sprintf('Could not determine the index of object with GUID %s', $document->getId()),
                1412801014
            );
        }


        $this->removeRawDataForIndex($index);

        $this->removeFromIndex($document);

        if ($this->contains($document)) {
            throw new RuntimeException(sprintf('Database still contains object %s', $document->getGuid()), 1413290094);
        }
        $this->state = self::STATE_DIRTY;
        SharedEventEmitter::emit(Event::DATABASE_DOCUMENT_REMOVED, array($document));
    }



    // MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
    // HELPER METHODS
    // MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW

    /**
     * Removes the raw data at the given index
     *
     * @param int $index
     */
    protected function removeRawDataForIndex($index)
    {
        $basicArray    = $this->rawData->toArray();
        $newArray      = array_merge(array_slice($basicArray, 0, $index), array_slice($basicArray, $index + 1));
        $this->rawData = SplFixedArray::fromArray($newArray);
    }

    /**
     * Removes the given data instance from the Indexes
     *
     * @param DocumentInterface $document
     */
    protected function removeFromIndex($document)
    {
        $this->rebuildIndexes();
//		foreach ($this->indexes as $indexInstance) {
//			$indexInstance->deleteEntry($document);
//		}
    }

    /**
     * Rebuild the indexes
     */
    protected function rebuildIndexes()
    {
        foreach ($this->indexes as $indexInstance) {
            $indexInstance->indexDatabase($this);
        }
    }


    /**
     * Returns all Document instances of this database
     *
     * @return DatabaseInterface[]
     */
    public function toArray()
    {
        return $this->toFixedArray()->toArray();
    }

    /**
     * Returns all Document instances of this database
     *
     * @return \SplFixedArray<DatabaseInterface>
     */
    public function toFixedArray()
    {
        return $this->rawData;
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
        $this->index++;
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
        return $this->index;
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
        return $this->index < $this->rawData->count();
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
        $this->index = 0;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Seeks to a position
     *
     * @link http://php.net/manual/en/seekableiterator.seek.php
     * @param int $position <p>
     *                      The position to seek to.
     *                      </p>
     * @return void
     */
    public function seek($position)
    {
        $this->index = $position;
    }
}