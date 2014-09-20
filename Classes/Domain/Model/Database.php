<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 25.08.14
 * Time: 21:30
 */

namespace Cundd\PersistentObjectStore\Domain\Model;

use Cundd\PersistentObjectStore\Core\ArrayException\IndexOutOfRangeException;
use Cundd\PersistentObjectStore\Core\ArrayException\InvalidIndexException;
use Cundd\PersistentObjectStore\Filter\Filter;
use Cundd\PersistentObjectStore\LogicException;
use Cundd\PersistentObjectStore\RuntimeException;
use Cundd\PersistentObjectStore\Utility\DebugUtility;
use Cundd\PersistentObjectStore\Utility\GeneralUtility;

/**
 * Database class which holds the Data instances
 *
 * Implementation with object creation on demand.
 *
 * @package Cundd\PersistentObjectStore\Domain\Model
 */
class Database implements DatabaseInterface, \Iterator, \Countable {
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
	 * Total object count
	 *
	 * @var int
	 */
	protected $totalCount = -1;

	/**
	 * Raw data array
	 *
	 * @var array
	 */
	protected $rawData = array();

	/**
	 * Collection of converted objects mapped to their database identifier
	 *
	 * @var array
	 */
	static protected $objectCollectionMap = array();


	/**
	 * Creates a new database
	 *
	 * @param string $identifier
	 * @param array $rawData
	 */
	function __construct($identifier, $rawData = array()) {
		GeneralUtility::assertDatabaseIdentifier($identifier);
		$this->identifier = $identifier;

		if ($rawData) {
			$this->setRawData($rawData);
		}

		$this->_increaseObjectCollectionReferenceCount();
	}

	function __clone() {
		$this->_increaseObjectCollectionReferenceCount();
	}

	function __wakeup() {
		$this->_increaseObjectCollectionReferenceCount();
	}

	function __destruct() {
		$this->_decreaseObjectCollectionReferenceCount();
		$this->_deleteObjectCollectionIfNecessary();
	}

	function __sleep() {
		$this->_decreaseObjectCollectionReferenceCount();
		$this->_deleteObjectCollectionIfNecessary();
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
	 * @param $rawData
	 */
	public function setRawData($rawData) {
		$this->rawData = $rawData;
		$this->totalCount = count($rawData);
	}

	/**
	 * Filters the database using the given comparisons
	 *
	 * @param array $comparisons
	 * @return \Cundd\PersistentObjectStore\Filter\FilterResultInterface
	 */
	public function filter($comparisons) {
		$filter = new Filter($comparisons);
		return $filter->filterCollection($this);
	}

	// MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
	// MANAGING OBJECTS
	// MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
	/**
	 * Returns if the database contains the given data instance
	 *
	 * @param DataInterface|string $dataInstance
	 * @return boolean
	 */
	public function contains($dataInstance){
		return $this->_contains($dataInstance);
	}

	/**
	 * Adds the given data instance to the database
	 *
	 * @param DataInterface $dataInstance
	 */
	public function add($dataInstance){
		$newIndex = $this->count();
		$this->_addDataInstanceAtIndex($dataInstance, $newIndex);
//		$this->objectCollection[$this->totalCount] = $dataInstance;
		$this->totalCount++;
	}

	/**
	 * Updates the given data instance in the database
	 *
	 * @param DataInterface $dataInstance
	 */
	public function update($dataInstance) {
		throw new LogicException('Missing implementation for ' . __METHOD__);
	}

	/**
	 * Removes the given data instance from the database
	 *
	 * @param DataInterface $dataInstance
	 */
	public function remove($dataInstance) {
		throw new LogicException('Missing implementation for ' . __METHOD__);
	}


	// MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
	// ITERATOR AND COUNTABLE
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
		if ($this->totalCount === -1) {
			$this->totalCount = count($this->rawData);
		}
		return $this->totalCount;
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Return the current element
	 *
	 * @link http://php.net/manual/en/iterator.current.php
	 * @return mixed Can return any type.
	 */
	public function current() {
		$currentIndex = $this->index;

		// Try to read the object from the object collection map
		$this->_prepareObjectCollectionMap();
		$currentObject = $this->_getObjectForIndex($currentIndex);
		if ($currentObject) {
//			DebugUtility::pl('found one');
			return $currentObject;
		}

//		DebugUtility::pl('convert one');
//		DebugUtility::pl('need to convert for index %s', $currentIndex);
//		DebugUtility::var_dump('need to convert for index %s', $currentIndex);
		$currentObject = $this->_convertDataAtIndexToObject($currentIndex);
		$this->_addDataInstanceAtIndex($currentObject, $currentIndex);
		return $currentObject;


//		if (!isset($this->objectCollection[$this->index])) {
//			$this->objectCollection[$this->index] = $this->_convertDataAtIndexToObject($this->index);
//		}
//		return $this->objectCollection[$this->index];
	}

	/**
	 * Converts the raw data at the given index to a Data instance
	 *
	 * @param integer $index
	 * @return DataInterface
	 */
	protected function _convertDataAtIndexToObject($index) {
		if (!isset($this->rawData[$index])) {
			DebugUtility::var_dump(
				__METHOD__ . ' valid',
				$this->index < $this->count() || isset($this->rawData[$this->index]) || $this->_getObjectForIndex($this->index),
				$this->index < $this->count(),
				isset($this->rawData[$this->index]),
				!!$this->_getObjectForIndex($this->index)
			);
			DebugUtility::var_dump(
				$index,
				$index < $this->count() || isset($this->rawData[$index]) || $this->_getObjectForIndex($index),
				$index < $this->count(),
				isset($this->rawData[$index]),
				!!$this->_getObjectForIndex($index)
			);
			throw new IndexOutOfRangeException('Invalid index ' . $index);

		}
//		if (!isset($this->rawData[$index])) throw new IndexOutOfRangeException('Invalid index ' . $index);
		$rawData    = $this->rawData[$index];
		$dataObject = new Data();
		$dataObject->setData($rawData);

		$dataObject->setDatabaseIdentifier($this->getIdentifier());
		$dataObject->setId(isset($rawMetaData['id']) ? $rawMetaData['id'] : NULL);
		$dataObject->setCreationTime(isset($rawMetaData['creation_time']) ? $rawMetaData['creation_time'] : NULL);
		$dataObject->setModificationTime(isset($rawMetaData['modification_time']) ? $rawMetaData['modification_time'] : NULL);

		return $dataObject;
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
		return $this->index < $this->count() || isset($this->rawData[$this->index]) || $this->_getObjectForIndex($this->index);
//		return $this->index < $this->count() || isset($this->objectCollection[$this->index]) || isset($this->rawData[$this->index]);
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

//	/**
//	 * Add all objects from the collection to the database
//	 *
//	 * @param array|\Traversable $collection
//	 */
//	public function attachAll($collection) {
//		foreach ($collection as $element) {
//			$this->attach($element);
//		}
//	}


	// MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
	// HELPER METHODS
	// MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
	/**
	 * Returns the Data instance for the given index or NULL if it does not exist
	 *
	 * @param integer $index
	 * @return DataInterface|NULL
	 */
	protected function _getObjectForIndex($index) {
		if (!is_integer($index) && !((string)(int)$index === $index)) {
			throw new InvalidIndexException('Offset could not be converted to integer', 1410167582);
		}

		if (isset(static::$objectCollectionMap[$this->getIdentifier()]['hash_to_index_map'][$index])) {
			$objectHash = static::$objectCollectionMap[$this->getIdentifier()]['hash_to_index_map'][$index];
			return static::$objectCollectionMap[$this->getIdentifier()]['objects'][$objectHash];
		}
		return NULL;
	}

	/**
	 * Returns if the database contains the given data instance
	 *
	 * @param DataInterface|string $dataInstance Actual Data instance or it'S object hash
	 * @return boolean
	 */
	protected function _contains($dataInstance){
		$databaseIdentifier = $this->getIdentifier();
		$this->_prepareObjectCollectionMap();
		if (is_object($dataInstance)) {
			$objectHash = spl_object_hash($dataInstance);
		} else {
			$objectHash = $dataInstance;
		}

		return isset(static::$objectCollectionMap[$databaseIdentifier]['objects'][$objectHash]);
	}

	/**
	 * Makes sure the object collection map contains an entry for the current database
	 */
	protected function _prepareObjectCollectionMap() {
		$databaseIdentifier = $this->getIdentifier();
		if (!isset(static::$objectCollectionMap[$databaseIdentifier])) {
			static::$objectCollectionMap[$databaseIdentifier] = array(
				'ref_count' => 0,
				'hash_to_index_map' => array(),
				'objects' => array()
			);
		}
	}

	/**
	 * Increases the reference count of the object collection map for the given database
	 */
	protected function _increaseObjectCollectionReferenceCount() {
		$this->_prepareObjectCollectionMap();
		static::$objectCollectionMap[$this->getIdentifier()]['ref_count']++;
	}

	/**
	 * Decreases the reference count of the object collection map for the given database
	 */
	protected function _decreaseObjectCollectionReferenceCount() {
		$this->_prepareObjectCollectionMap();
		static::$objectCollectionMap[$this->getIdentifier()]['ref_count']--;
	}

	/**
	 * Removes the cached object collection for the given database if the reference count is less than 1
	 */
	protected function _deleteObjectCollectionIfNecessary() {
		$databaseIdentifier = $this->getIdentifier();
		if (static::$objectCollectionMap[$databaseIdentifier]['ref_count'] < 1) {
			unset(static::$objectCollectionMap[$databaseIdentifier]);
		}
	}

	/**
	 * Adds the given data instance to the database with the given index
	 *
	 * @param DataInterface $dataInstance
	 * @param integer $index
	 */
	public function _addDataInstanceAtIndex($dataInstance, $index){
		$objectUid = spl_object_hash($dataInstance);
		$databaseIdentifier = $this->getIdentifier();
		if ($this->_contains($dataInstance)) throw new RuntimeException(
			sprintf('Object with hash %s already exists in the database', $objectUid),
			1411205350
		);
		static::$objectCollectionMap[$databaseIdentifier]['objects'][$objectUid] = $dataInstance;

		static::$objectCollectionMap[$databaseIdentifier]['hash_to_index_map'][$index] = $objectUid;
//		$this->objectCollection[$index] = $dataInstance;
	}
}