<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 25.08.14
 * Time: 21:30
 */

namespace Cundd\PersistentObjectStore\Domain\Model;

use Cundd\PersistentObjectStore\Core\ArrayException\IndexOutOfRangeException;
use Cundd\PersistentObjectStore\Filter\Filter;
use Cundd\PersistentObjectStore\LogicException;
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
	 * Collection of converted objects
	 *
	 * @var array
	 */
	protected $objectCollection = array();

	/**
	 * Raw data array
	 *
	 * @var array
	 */
	protected $rawData = array();


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
	 * Adds the given data instance to the database
	 *
	 * @param DataInterface $dataInstance
	 */
	public function add($dataInstance){
		$this->objectCollection[$this->totalCount] = $dataInstance;
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
		if (!isset($this->objectCollection[$this->index])) {
			if (!isset($this->rawData[$this->index])) throw new IndexOutOfRangeException('Invalid index ' . $this->index);
			$rawData    = $this->rawData[$this->index];
			$dataObject = new Data();
			$dataObject->setData($rawData);

			$dataObject->setDatabaseIdentifier($this->getIdentifier());
			$dataObject->setId(isset($rawMetaData['id']) ? $rawMetaData['id'] : NULL);
			$dataObject->setCreationTime(isset($rawMetaData['creation_time']) ? $rawMetaData['creation_time'] : NULL);
			$dataObject->setModificationTime(isset($rawMetaData['modification_time']) ? $rawMetaData['modification_time'] : NULL);

			$this->objectCollection[$this->index] = $dataObject;
		}
		return $this->objectCollection[$this->index];
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
		return $this->index < $this->count() || isset($this->objectCollection[$this->index]) || isset($this->rawData[$this->index]);
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





}