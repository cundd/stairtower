<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 04.09.14
 * Time: 21:01
 */

namespace Cundd\PersistentObjectStore\Filter;

use Cundd\PersistentObjectStore\Core\IndexArray;
use Cundd\PersistentObjectStore\Domain\Model\Database;
use Cundd\PersistentObjectStore\Utility\DebugUtility;

/**
 * Result of a filtered collection
 *
 * @package Cundd\PersistentObjectStore\Filter
 */
class FilterResult extends IndexArray implements FilterResultInterface {
	/**
	 * Collection to filter
	 *
	 * @var Database|\Iterator
	 */
	protected $collection;

	/**
	 * Filter to apply to the collection
	 *
	 * @var FilterInterface
	 */
	protected $filter;

//	/**
//	 * Current index of the filtered collection
//	 *
//	 * @var int
//	 */
//	protected $filteredCollectionCurrentIndex = 0;


	/**
	 * Number of filtered objects
	 *
	 * @var int
	 */
	protected $filteredCount = -1;



	/**
	 * @param Database|\Iterator $originalCollection
	 * @param FilterInterface    $filter
	 */
	function __construct($originalCollection, $filter) {
		$this->collection = $this->_cloneCollection($originalCollection);
		$this->filter     = $filter;
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Return the current element
	 *
	 * @link http://php.net/manual/en/iterator.current.php
	 * @return mixed Can return any type.
	 */
	public function current() {
		DebugUtility::var_dump(__METHOD__, $this->length);
		$this->_initFilteredCollection();
		DebugUtility::var_dump($this->key());

		return parent::current();
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Move forward to next element
	 *
	 * @link http://php.net/manual/en/iterator.next.php
	 * @return void Any returned value is ignored.
	 */
	public function next() {
		DebugUtility::var_dump(__METHOD__);
		$this->_findNext();
		parent::next();
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Return the key of the current element
	 *
	 * @link http://php.net/manual/en/iterator.key.php
	 * @return mixed scalar on success, or null on failure.
	 */
	public function key() {
		DebugUtility::var_dump(__METHOD__);

		$this->_initFilteredCollection();
		return parent::key();
//		return $this->->key();
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
		DebugUtility::var_dump(__METHOD__);

		$this->_initFilteredCollection();
		parent::valid();
//		return $this->filteredCollection->valid();
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Rewind the Iterator to the first element
	 *
	 * @link http://php.net/manual/en/iterator.rewind.php
	 * @return void Any returned value is ignored.
	 */
	public function rewind() {
		DebugUtility::var_dump(__METHOD__);

		$this->currentIndex = 0;
		$this->collection->rewind();

//		$this->_initFilteredCollection();
//		$this->filteredCollection->rewind();
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
	public function count() {
		DebugUtility::var_dump(__METHOD__);

		if ($this->length === -1) {
			$this->_findAll();
		}
		return parent::count();
	}

	/**
	 * Initializes the filtered collection
	 */
	protected function _initFilteredCollection() {
		if ($this->filteredCollectionCurrentIndex === 0) {
			$this->_findNext();
		}
	}

	/**
	 * Find the next matching object
	 *
	 * Returns NULL if none was found
	 *
	 * @return mixed
	 */
	protected function _findNext() {
		DebugUtility::var_dump(__METHOD__);
		// If the filtered collection is fully populated
		if ($this->filteredCount !== -1) {
			$this->filteredCollection->next();
			if ($this->filteredCollection->valid()) {
				return $this->filteredCollection->current();
			}
			return NULL;
		}
		if (!$this->filteredCollection) {
			$this->filteredCollection = new \SplFixedArray(1);
		}

		$foundObject = NULL;

		$collection = $this->collection;
		$filter     = $this->filter;

		DebugUtility::var_dump('coljey:', $collection->key());
		DebugUtility::var_dump($collection->valid());
		// Loop through the collection until one matching object was found
		while ($collection->valid()) {
			$item = $collection->current();
//			echo 'check ' . spl_object_hash($item) . PHP_EOL;
			if ($filter->checkItem($item)) {
				$foundObject = $item;
				$collection->next();
				var_dump($item);
				break;
			}
			$collection->next();
		}

		if (!$foundObject) {
			throw new \Exception('nothing found');
		}
		$this->filteredCollection->setSize($this->filteredCollectionCurrentIndex + 1);
		$this->filteredCollection[$this->filteredCollectionCurrentIndex] = $foundObject;
		$this->filteredCollectionCurrentIndex++;
		return $foundObject;
	}

	/**
	 * Find all matching objects
	 *
	 * @return \SplObjectStorage
	 */
	protected function _findAll() {
//		unset($this->filteredCollection);

		$collection = $this->collection;
//		$collection->rewind();
		$filter = $this->filter;

		$filteredCollection = $this->filteredCollection;
		if (!$filteredCollection) {
			$filteredCollection = new \SplFixedArray($collection->count());
		}

		$filteredCollectionCurrentIndex = $this->filteredCollectionCurrentIndex;
		DebugUtility::var_dump($filteredCollectionCurrentIndex);
		DebugUtility::var_dump($collection->count());
		DebugUtility::var_dump($collection->key());

		while ($collection->valid()) {
			$item = $collection->current();
			if ($filter->checkItem($item)) {
				DebugUtility::var_dump('ind:', $filteredCollectionCurrentIndex);
				$filteredCollection[$filteredCollectionCurrentIndex] = $item;
				$filteredCollectionCurrentIndex++;
			}
			$collection->next();
		}


		// Shrink the array
		$filteredCollection->setSize($filteredCollectionCurrentIndex);

		// TODO: reset the position if count is called in the middle of something
		$filteredCollection->rewind();
		$this->filteredCollection = $filteredCollection;
		$this->filteredCollectionCurrentIndex = $filteredCollectionCurrentIndex;
		$this->filteredCount = $filteredCollectionCurrentIndex;

		DebugUtility::var_dump($this->filteredCollection);
		return $this->filteredCollection;
	}

	/**
	 * Creates a deep clone of the given colleciton
	 *
	 * @param Database|\Iterator $originalCollection
	 * @return \SplObjectStorage
	 */
	protected function _cloneCollection($originalCollection){
		$collection = new \SplObjectStorage();
		foreach ($originalCollection as $item) {
			$collection->attach(clone $item);
		}
		$collection->rewind();
		DebugUtility::var_dump('L:' . __LINE__, $collection->key());

		return $collection;
	}
} 