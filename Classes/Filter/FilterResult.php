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
use Cundd\PersistentObjectStore\Domain\Model\DatabaseInterface;
use Cundd\PersistentObjectStore\Exception\ImmutableException;
use Cundd\PersistentObjectStore\Immutable;
use Cundd\PersistentObjectStore\Utility\DebugUtility;

/**
 * Result of a filtered collection
 *
 * @package Cundd\PersistentObjectStore\Filter
 */
class FilterResult extends IndexArray implements FilterResultInterface, Immutable {
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

	/**
	 * Defines if the collection has been filtered once
	 *
	 * @var bool
	 */
	protected $fullyFiltered = FALSE;


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
		$this->_initFilteredCollection();
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
		$this->_initFilteredCollection();

		$this->_findNext();
		$this->currentIndex++;
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Return the key of the current element
	 *
	 * @link http://php.net/manual/en/iterator.key.php
	 * @return mixed scalar on success, or null on failure.
	 */
	public function key() {
		$this->_initFilteredCollection();
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
	public function valid() {
		$this->_initFilteredCollection();
		return parent::valid();
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Rewind the Iterator to the first element
	 *
	 * @link http://php.net/manual/en/iterator.rewind.php
	 * @return void Any returned value is ignored.
	 */
	public function rewind() {
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
	public function count() {
		if (!$this->fullyFiltered) {
			$this->_findAll();
		}
		return parent::count();
	}

	/**
	 * Returns the filtered items as array
	 *
	 * @return array
	 */
	public function toArray() {
		if (!$this->fullyFiltered) {
			$this->_findAll();
		}
		return $this->elements;
	}

	/**
	 * Initializes the filtered collection
	 */
	protected function _initFilteredCollection() {
		if ($this->length === 0) {
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
		// If the filtered collection is fully populated
		if ($this->fullyFiltered) {
			return parent::current();
		}
		$foundObject = NULL;

		$collection = $this->collection;
		$filter     = $this->filter;

		$useRaw = method_exists($collection, 'currentRaw');
		DebugUtility::pl('use raw ' . ($useRaw ? 'yes' : 'no'));

		// Loop through the collection until one matching object was found
		while ($collection->valid()) {
			if ($useRaw) {
				$item = $collection->currentRaw();
			} else {
				$item = $collection->current();
			}
//			echo 'check ' . spl_object_hash($item) . PHP_EOL;
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
			$this->fullyFiltered = TRUE;
		}

//		if (!$foundObject) {
//			throw new \Exception('nothing found');
//		}

		parent::push($foundObject);
		return $foundObject;
	}

	/**
	 * Find all matching objects
	 */
	protected function _findAll() {
		$start = microtime(TRUE);

		$collection = $this->collection;
		$filter     = $this->filter;

		$useRaw = method_exists($collection, 'currentRaw');
		DebugUtility::pl('use raw ' . ($useRaw ? 'yes' : 'no'));


		while ($collection->valid()) {
			if ($useRaw) {
				$item = $collection->currentRaw();
			} else {
				$item = $collection->current();
			}
			if ($filter->checkItem($item)) {
				if ($useRaw) {
					$item = $collection->current();
				}
				parent::push($item);
			}
			$collection->next();
		}


		$end = microtime(TRUE);

//		printf("Full filter: %0.6f\n", $end - $start);

		$this->fullyFiltered = TRUE;
	}

	/**
	 * Creates a deep clone of the given collection
	 *
	 * @param Database|\Iterator $originalCollection
	 * @return \SplObjectStorage
	 */
	protected function _cloneCollection($originalCollection) {
		// If the collection is immutable just return it
		if ($originalCollection instanceof Immutable) {
			return $originalCollection;
		}
		if ($originalCollection instanceof DatabaseInterface) {
			return clone $originalCollection;
		}

		DebugUtility::printMemorySample();

		$start = microtime(TRUE);
		$collection = new \SplFixedArray($originalCollection->count());
		$i = 0;
		foreach ($originalCollection as $item) {
			$collection[$i] = clone $item;
			$i++;
		}
		$collection->rewind();

		DebugUtility::printMemorySample();
		$end = microtime(TRUE);
		printf("Clone Time: %0.6f\n", $end - $start);
		return $collection;
	}


	/**
	 * Adds an element to the end of the array
	 *
	 * @param mixed $value
	 * @throws \Cundd\PersistentObjectStore\Exception\ImmutableException
	 */
	public function push($value) {
		throw new ImmutableException('Can not modify this immutable', 1410628420);
	}

	/**
	 * Pops the element from the end of the array
	 *
	 * @throws \Cundd\PersistentObjectStore\Exception\ImmutableException
	 * @return mixed
	 */
	public function pop() {
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
	public function offsetSet($offset, $value) {
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
	public function offsetUnset($offset) {
		throw new ImmutableException('Can not modify this immutable', 1410628420);
	}
} 