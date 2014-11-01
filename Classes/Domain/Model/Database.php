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
use Cundd\PersistentObjectStore\RuntimeException;
use Cundd\PersistentObjectStore\Utility\DebugUtility;
use Cundd\PersistentObjectStore\Utility\GeneralUtility;
use SplFixedArray;

/**
 * Database class which holds the Document instances
 *
 * Implementation with object creation on demand.
 *
 * @package Cundd\PersistentObjectStore\Domain\Model
 */
class Database implements DatabaseInterface, DatabaseRawDataInterface, ArrayableInterface {
	/**
	 * Raw data array
	 *
	 * @var \SplFixedArray
	 */
	protected $rawData;

	/**
	 * Converted objects
	 *
	 * @var \SplFixedArray
	 */
	protected $objectData;

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
	 * Creates a new database
	 *
	 * @param string $identifier
	 * @param array  $rawData
	 */
	function __construct($identifier, $rawData = array()) {
//		static $createdDatabases = array();
//		if (isset($createdDatabases[$identifier])) {
//			throw new InvalidDatabaseException(sprintf('Database with identifier %s already created', $identifier), 1413721539);
//		}
//		$createdDatabases[$identifier] = TRUE;

		GeneralUtility::assertDatabaseIdentifier($identifier);
		$this->identifier = $identifier;

		if ($rawData) {
			$this->setRawData($rawData);
		} else {
			$this->rawData = new SplFixedArray(0);
			$this->objectData = new SplFixedArray(0);
		}

//		$this->_prepareObjectCollectionMap();
//		$this->_increaseObjectCollectionReferenceCount();
	}

	/**
	 * Returns the database identifier
	 *
	 * @return string
	 */
	public function getIdentifier() {
		return $this->identifier;
	}

	/**
	 * Sets the raw data
	 *
	 * @param \SplFixedArray|array|\Iterator $rawData
	 */
	public function setRawData($rawData) {
		if ($rawData instanceof SplFixedArray) {
			$this->rawData = $rawData;
		} else if (is_array($rawData)) {
			$this->rawData = SplFixedArray::fromArray($rawData);
		} else if ($rawData instanceof \Iterator) {
			$this->rawData = SplFixedArray::fromArray(iterator_to_array($rawData));
		} else {
			throw new InvalidCollectionException('Could not set raw data', 1412017652);
		}
		$this->objectData = new SplFixedArray($this->rawData->getSize());
	}

	/**
	 * Returns the raw data
	 *
	 * @return \SplFixedArray
	 * @internal
	 */
	public function getRawData() {
		if (!$this->rawData) {
			$this->rawData = new \SplFixedArray(0);
		}
		return $this->rawData;
	}

	/**
	 * Returns the current raw data
	 *
	 * @link http://php.net/manual/en/iterator.current.php
	 * @return mixed Can return any type.
	 */
	public function currentRaw() {
		$dataInstance = $this->_getRawDataForIndex($this->index);
		if ($dataInstance === FALSE) throw new IndexOutOfRangeException('Invalid index ' . $this->index, 1411316363);
		return $dataInstance;
	}


	/**
	 * Filters the database using the given comparisons
	 *
	 * @param array|ComparisonInterface $comparisons
	 * @return \Cundd\PersistentObjectStore\Filter\FilterResultInterface
	 */
	public function filter($comparisons) {
		if (!is_array($comparisons)) {
			$comparisons = func_get_args();
		}
		$filter = new Filter($comparisons);
		return $filter->filterCollection($this);
	}

	// MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
	// MANAGING OBJECTS
	// MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
	/**
	 * Returns if the database contains the given data instance
	 *
	 * @param DocumentInterface|string $dataInstance Actual Document instance or it's GUID
	 * @return boolean
	 */
	public function contains($dataInstance) {
		if (is_string($dataInstance)) {
			$identifier = $dataInstance;
		} elseif ($dataInstance instanceof DocumentInterface) {
			$this->_assertDataInstancesDatabaseIdentifier($dataInstance);
			$identifier = $dataInstance->getId();
		} else {
			throw new RuntimeException("Given value $dataInstance is of type " . gettype($dataInstance));
		}
		return $this->findByIdentifier($identifier) ? TRUE : FALSE;
	}

	/**
	 * Returns the object with the given identifier
	 *
	 * @param string $identifier
	 * @return DocumentInterface|NULL
	 */
	public function findByIdentifier($identifier) {
		$count = $this->count();
		$i     = 0;

		if ($count === 0) {
			return NULL;
		}
		do {
//			DebugUtility::pl($i);
			if (isset($this->objectData[$i])) {
				$foundObject = $this->objectData[$i];
			} else {
				$foundObject = $this->_setObjectDataForIndex($this->_convertDataAtIndexToObject($i), $i);
			}

			if ($foundObject instanceof DocumentInterface && $foundObject->getId() === $identifier) {
				return $foundObject;
			}
		} while (++$i < $count);
		return NULL;
	}

	/**
	 * Adds the given data instance to the database
	 *
	 * @param DocumentInterface $dataInstance
	 */
	public function add($dataInstance) {
		$this->_assertDataInstancesDatabaseIdentifier($dataInstance);
		$currentCount = $this->count();

		if ($this->contains($dataInstance)) {
			throw new InvalidDataException(
				sprintf('Object with GUID %s already exists in the database. Maybe the values of the identifier \'%s\' are not expressive', $dataInstance->getGuid(), $dataInstance->getIdentifierKey()),
				1411205350
			);
		}

		$this->objectData->setSize($currentCount + 1);
		$this->_setObjectDataForIndex($dataInstance, $currentCount);

		$this->rawData->setSize($currentCount + 1);
		$this->_setRawDataForIndex($dataInstance->getData(), $currentCount);

		SharedEventEmitter::emit(Event::DATABASE_DOCUMENT_ADDED, array($dataInstance));
	}

	/**
	 * Updates the given data instance in the database
	 *
	 * @param DocumentInterface $dataInstance
	 */
	public function update($dataInstance) {
		$this->_assertDataInstancesDatabaseIdentifier($dataInstance);

		if (!$this->contains($dataInstance)) {
			throw new InvalidDataException(
				sprintf('Object with GUID %s does not exist in the database. Maybe the values of the identifier \'%s\' are not expressive', $dataInstance->getGuid(), $dataInstance->getIdentifierKey()),
				1412800596
			);
		}

		$index           = $this->_getIndexForIdentifier($dataInstance->getId());
		$oldDataInstance = $this->_getObjectDataForIndex($index);
		if (!$oldDataInstance) throw new InvalidDataException('No data instance found to replace', 1413711010);
		if ($dataInstance->getId() !== $oldDataInstance->getId()) {
			throw new InvalidDataException(
				sprintf(
					'Given identifier "%s" does not match the found instance\'s identifier "%s"',
					$dataInstance->getId(),
					$oldDataInstance->getId()
				),
				1413711010
			);
		}

		$this->_setObjectDataForIndex($dataInstance, $index);
		$this->_setRawDataForIndex($dataInstance->getData(), $index);


//		$identifier = ($dataInstance instanceof DocumentInterface) ? $dataInstance->getGuid() : spl_object_hash($dataInstance);
//		$this->objectCollectionMap[$this->identifier][self::OBJ_COL_KEY_GUID_TO_OBJECT][$identifier] = $dataInstance;

		SharedEventEmitter::emit(Event::DATABASE_DOCUMENT_UPDATED, array($dataInstance));
	}

	/**
	 * Removes the given data instance from the database
	 *
	 * @param DocumentInterface $dataInstance
	 */
	public function remove($dataInstance) {
		$this->_assertDataInstancesDatabaseIdentifier($dataInstance);

		if (!$this->contains($dataInstance)) {
			throw new InvalidDataException(
				sprintf('Object with GUID %s does not exist in the database. Maybe the values of the identifier \'%s\' are not expressive', $dataInstance->getGuid(), $dataInstance->getIdentifierKey()),
				1412800595
			);
		}

		$index = $this->_getIndexForIdentifier($dataInstance->getId());
		if ($index === -1) {
			throw new RuntimeException(
				sprintf('Could not determine the index of object with GUID %s', $dataInstance->getId()),
				1412801014
			);
		}

		$this->_removeObjectDataForIndex($index);
		$this->_removeRawDataForIndex($index);

//		$databaseIdentifier = $this->identifier;
//		if (!$this->contains($dataInstance)) {
//			throw new InvalidDataException(
//				sprintf('Object with GUID %s does not exist in the database. Maybe the values of the identifier \'%s\' are not expressive', $objectUid, $dataInstance->getIdentifierKey()),
//				1412800595
//			);
//		}
//
//		$index = array_search($objectUid, $this->objectCollectionMap[$databaseIdentifier][self::OBJ_COL_KEY_INDEX_TO_GUID], TRUE);
//		if ($index === FALSE) {
//			throw new RuntimeException(
//				sprintf('Could not determine the index of object with GUID %s', $objectUid),
//				1412801014
//			);
//		}
//		unset($this->objectCollectionMap[$databaseIdentifier][self::OBJ_COL_KEY_GUID_TO_OBJECT][$objectUid]);
//		unset($this->objectCollectionMap[$databaseIdentifier][self::OBJ_COL_KEY_INDEX_TO_GUID][$index]);
//
//		if ($index > $this->count()) {
//			throw new InvalidIndexException(
//				sprintf('Index %d out of bound', $index),
//				1412277617
//			);
//		}
//
//		$this->totalCount--;
//		if ($index < $this->totalCount) {
//			$this->rawData->setSize($this->totalCount);
//		}
//		if ($this->rawData->offsetExists($index)) {
//			$this->rawData[$index] = NULL;
//		}
//
		if ($this->contains($dataInstance)) {
			throw new RuntimeException(sprintf('Database still contains object %s', $dataInstance->getGuid()), 1413290094);
		}
		SharedEventEmitter::emit(Event::DATABASE_DOCUMENT_REMOVED, array($dataInstance));
	}



	// MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
	// ITERATOR, COUNTABLE AND SEEKABLE
	// MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
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
	public function count() {
		if ($this->rawData->count() !== $this->objectData->count()) {
			throw new RuntimeException(sprintf('Object and raw data count mismatch (%d/%d)', $this->rawData->count(), $this->objectData->count()), 1413713529);
		}
		return $this->rawData->count();
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Return the current element
	 *
	 * @link http://php.net/manual/en/iterator.current.php
	 * @return mixed Can return any type.
	 */
	public function current() {
		return $this->_getObjectDataForIndexOrTransformIfNotExists($this->index);
	}

	/**
	 * Returns all Document instances of this database
	 *
	 * @return \SplFixedArray<DatabaseInterface>
	 */
	public function toFixedArray() {
		$count = $this->count();
		$i     = 0;

		do {
			$this->_getObjectDataForIndexOrTransformIfNotExists($i);
		} while (++$i < $count);

		return $this->objectData;
	}

	/**
	 * Returns all Document instances of this database
	 *
	 * @return array<DatabaseInterface>
	 */
	public function toArray() {
		return $this->toFixedArray()->toArray();
	}


	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Move forward to next element
	 *
	 * @link http://php.net/manual/en/iterator.next.php
	 * @return void Any returned value is ignored.
	 */
	public function next() {
		$this->index++;
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Return the key of the current element
	 *
	 * @link http://php.net/manual/en/iterator.key.php
	 * @return mixed scalar on success, or null on failure.
	 */
	public function key() {
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
	public function valid() {
		return $this->index < $this->rawData->count() || $this->index < $this->objectData->count();
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Rewind the Iterator to the first element
	 *
	 * @link http://php.net/manual/en/iterator.rewind.php
	 * @return void Any returned value is ignored.
	 */
	public function rewind() {
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
	public function seek($position) {
		$this->index = $position;
	}



	// MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
	// HELPER METHODS
	// MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
	/**
	 * Checks if the Document instance's database identifier is correct
	 *
	 * @param DocumentInterface $dataInstance
	 */
	protected function _assertDataInstancesDatabaseIdentifier($dataInstance) {
		if (!is_object($dataInstance)) throw new InvalidDataException(sprintf('Given data instance is not of type object but %s', gettype($dataInstance)), 1412859398);
		if (!$dataInstance->getDatabaseIdentifier()) {
			if ($dataInstance instanceof Document) {
				$dataInstance->setDatabaseIdentifier($this->identifier);
			}
		} else if ($dataInstance->getDatabaseIdentifier() !== $this->identifier) {
			throw new DatabaseMismatchException(
				sprintf(
					'The given Document instance does not belong to this database (Document instance database identifier: %s, Database identifier: %s',
					$dataInstance->getDatabaseIdentifier(),
					$this->identifier
				),
				1411315947
			);
		}
	}

	/**
	 * Checks if the raw data's identifier is defined
	 *
	 * @param int $index
	 * @return array Returns the prepared data
	 */
	protected function _setRawDataIdentifierIfNotSetForIndex($index) {
		$rawData = $this->rawData[$index];
		if (!isset($rawData[Constants::DATA_ID_KEY]) || $rawData[Constants::DATA_ID_KEY]) {
			$identifier = $this->_tryToReadIdentifierFromRawData($rawData);
			if (!$identifier) {
				$identifier = sprintf('stairtower_%s_%s_document_%d',
					Constants::VERSION,
					getmypid(),
					time()
				);
			}
			$identifier                      = sha1($identifier);
			$rawData[Constants::DATA_ID_KEY] = $identifier;
			$this->rawData[$index] = $rawData;
		}
		return $rawData;
	}

	/**
	 * Tries to read the best identifier from the raw data
	 *
	 * @param array $rawData
	 * @return mixed Returns the identifier if one is found otherwise FALSE
	 */
	protected function _tryToReadIdentifierFromRawData($rawData) {
		// If no identifier key is defined check the most common
		$commonIdentifiers = array('id', 'uid', 'email');
		foreach ($commonIdentifiers as $identifier) {
			if (isset($rawData[$identifier]) && $rawData[$identifier]) {
				return $identifier;
			}
		}
		return FALSE;
	}

	/**
	 * Returns the Document instance at the given index or sets it if it is not already set
	 *
	 * @param int $index
	 * @return bool|DocumentInterface
	 */
	protected function _getObjectDataForIndexOrTransformIfNotExists($index) {
		$dataInstance = $this->_getObjectDataForIndex($index);
		if (!$dataInstance) {
			$dataInstance = $this->_setObjectDataForIndex($this->_convertDataAtIndexToObject($index), $index);
		}
		return $dataInstance;
	}

	/**
	 * @see _getObjectDataForIndexOrTransformIfNotExists()
	 */
	public function getObjectDataForIndexOrTransformIfNotExists($index) {
		return $this->_getObjectDataForIndexOrTransformIfNotExists($index);
	}

	/**
	 * Returns the Document instance at the given index or FALSE if it is not already set
	 *
	 * @param int $index
	 * @return bool|DocumentInterface
	 */
	protected function _getObjectDataForIndex($index) {
		if (isset($this->objectData[$index])) {
			return $this->objectData[$index];
		}
		return FALSE;
	}

	/**
	 * Sets the Document instance at the given index
	 *
	 * @param DocumentInterface $object
	 * @param int           $index
	 * @return \Cundd\PersistentObjectStore\Domain\Model\DocumentInterface Returns the given object
	 */
	protected function _setObjectDataForIndex($object, $index) {
		if ($index >= $this->objectData->getSize()) throw new InvalidIndexException("Index $index out of range", 1413712508);
		$this->objectData[$index] = $object;
		return $object;
	}

	/**
	 * Removes the Document instance at the given index
	 *
	 * @param int $index
	 * @return void
	 */
	protected function _removeObjectDataForIndex($index) {
		$basicArray       = $this->objectData->toArray();
		$newArray         = array_merge(array_slice($basicArray, 0, $index), array_slice($basicArray, $index + 1));
		$this->objectData = SplFixedArray::fromArray($newArray);
	}

	/**
	 * Returns the raw data at the given index or FALSE if it is not set
	 *
	 * @param int $index
	 * @return bool|mixed
	 */
	protected function _getRawDataForIndex($index) {
		if (isset($this->rawData[$index])) {
			return $this->rawData[$index];
		}
		return FALSE;
	}

	/**
	 * Sets the raw data at the given index
	 *
	 * @param mixed $data
	 * @param int   $index
	 *
	 * @return mixed Returns the given data
	 */
	protected function _setRawDataForIndex($data, $index) {
		$this->rawData[$index] = $data;
		return $data;
	}

	/**
	 * Removes the raw data at the given index
	 *
	 * @param int $index
	 */
	protected function _removeRawDataForIndex($index) {
		$basicArray    = $this->rawData->toArray();
		$newArray      = array_merge(array_slice($basicArray, 0, $index), array_slice($basicArray, $index + 1));
		$this->rawData = SplFixedArray::fromArray($newArray);
	}

	/**
	 * Converts the raw data at the given index to a Document instance
	 *
	 * @param integer $index
	 * @return DocumentInterface
	 */
	protected function _convertDataAtIndexToObject($index) {
		if (isset($this->rawData[$index]) && $this->rawData[$index] === NULL) {
			return NULL;
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
			throw new IndexOutOfRangeException('Invalid index ' . $index, 1411316363);

		}
//		if (!isset($this->rawData[$index])) throw new IndexOutOfRangeException('Invalid index ' . $index);
		$rawData    = $this->rawData[$index];
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
	 * Returns the index for the given identifier or -1 if it does not exist
	 *
	 * @param string $identifier
	 * @return int
	 */
	protected function _getIndexForIdentifier($identifier) {
		$count         = $this->count();
		$i             = 0;
		$matchingIndex = -1;

		do {
			$foundObject = $this->_getObjectDataForIndex($i);
			if ($foundObject instanceof DocumentInterface && $foundObject->getId() === $identifier) {
				$matchingIndex = $i;
				break;
			}
			$rawData = $this->_setRawDataIdentifierIfNotSetForIndex($i);
			if ($rawData[Constants::DATA_ID_KEY] === $identifier) {
				$matchingIndex = $i;
				break;
			}
		} while (++$i < $count);
		return $matchingIndex;
	}
}