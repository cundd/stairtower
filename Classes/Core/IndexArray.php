<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 08.09.14
 * Time: 10:42
 */

namespace Cundd\PersistentObjectStore\Core;
use Cundd\PersistentObjectStore\Core\ArrayException\IndexOutOfRangeException;
use Cundd\PersistentObjectStore\Core\ArrayException\InvalidIndexException;

/**
 * The IndexArray class provides the main functionalities of array
 *
 * The main differences between a IndexArray and a normal PHP array is that it allows only integers as indexes.
 * The advantage is that it allows a faster array implementation.
 *
 * @package Cundd\PersistentObjectStore\Core
 */
class IndexArray implements \Iterator, \ArrayAccess, \Countable {
	/**
	 * Internal element store
	 *
	 * @var array
	 */
	protected $elements = array();

	/**
	 * Index of the element the array currently points to
	 *
	 * @var int
	 */
	protected $currentIndex = 0;

	/**
	 * Current length of the array
	 *
	 * @var int
	 */
	protected $length = 0;

	/**
	 * Creates a new array
	 *
	 * @param array $elements
	 */
	function __construct($elements = NULL) {
		if ($elements !== NULL) {
			$this->initWithArray($elements);
		}
	}

	/**
	 * Initialize the array with the elements from the given array
	 *
	 * @param array|\Iterator $elements
	 * @return $this
	 */
	public function initWithArray($elements){
		$this->elements = array();
		$tempElements = array();
		foreach ($elements as $item) {
			$tempElements[] = $item;
		}
		$this->elements = $tempElements;
		$this->length = count($tempElements);
		$this->currentIndex = 0;
		return $this;
	}

	/**
	 * Returns the first element
	 *
	 * @throws ArrayException\IndexOutOfRangeException if the array is empty
	 * @return mixed
	 */
	public function first() {
		if (!$this->length) throw new IndexOutOfRangeException('Array is empty', 1410178600);
		return $this->elements[0];
	}


	/**
	 * Returns the last element
	 *
	 * @throws ArrayException\IndexOutOfRangeException if the array is empty
	 * @return mixed
	 */
	public function last() {
		if (!$this->length) throw new IndexOutOfRangeException('Array is empty', 1410178600);
		return $this->elements[$this->length - 1];
	}

	/**
	 * Adds an element to the end of the array
	 *
	 * @param mixed $value
	 */
	public function push($value) {
		$this->elements[$this->length] = $value;
		$this->length++;
	}

	/**
	 * Pops the element from the end of the array
	 *
	 * @return mixed
	 */
	public function pop() {
		$lastIndex = --$this->length;
		$lastElement = $this->elements[$lastIndex];
		unset($this->elements[$lastIndex]);
		return $lastElement;
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Return the current element
	 *
	 * @link http://php.net/manual/en/iterator.current.php
	 * @throws ArrayException\IndexOutOfRangeException
	 * @return mixed Can return any type.
	 */
	public function current() {
		if ($this->currentIndex >= $this->length) throw new IndexOutOfRangeException('Index out of range', 1410183473);
		if (!isset($this->elements[$this->currentIndex])) {
			return NULL;
		}
		return $this->elements[$this->currentIndex];
//		return current($this->elements);
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Move forward to next element
	 *
	 * @link http://php.net/manual/en/iterator.next.php
	 * @return void Any returned value is ignored.
	 */
	public function next() {
		$this->currentIndex++;
		// next($this->elements);
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Return the key of the current element
	 *
	 * @link http://php.net/manual/en/iterator.key.php
	 * @return mixed scalar on success, or null on failure.
	 */
	public function key() {
		return $this->currentIndex;
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
		return $this->currentIndex < $this->length;
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
		return $this->length;
	}


	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Whether a offset exists
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetexists.php
	 * @param mixed $offset <p>
	 *                      An offset to check for.
	 *                      </p>
	 * @return boolean true on success or false on failure.
	 *                      </p>
	 *                      <p>
	 *                      The return value will be casted to boolean if non-boolean was returned.
	 */
	public function offsetExists($offset) {
		$offset = $this->_intValue($offset);
		if ($offset === NULL) {
			return FALSE;
		}
		return $offset < $this->length;
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Offset to retrieve
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetget.php
	 * @param mixed $offset <p>
	 *                      The offset to retrieve.
	 *                      </p>
	 * @throws ArrayException\InvalidIndexException
	 * @return mixed Can return all value types.
	 */
	public function offsetGet($offset) {
		$offset = $this->_intValue($offset);
		if ($offset === NULL) throw new InvalidIndexException('Offset could not be converted to integer', 1410167582);
		if ($offset >= $this->length) throw new IndexOutOfRangeException('Offset is out of range', 1410167584);

		if (isset($this->elements[$offset])) {
			return $this->elements[$offset];
		}
		return NULL;
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
	 * @throws ArrayException\InvalidIndexException
	 * @return void
	 */
	public function offsetSet($offset, $value) {
		$offset = $this->_intValue($offset);
		if ($offset === NULL) throw new InvalidIndexException('Offset could not be converted to integer', 1410167582);
		if ($offset > $this->length) throw new IndexOutOfRangeException('Offset is out of range. Current maximum offset is ' . $this->length, 1410167584);

		if ($offset == $this->length) { // Push the element if needed
			$this->push($value);
		} else {
			$this->elements[$offset] = $value;
		}
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Offset to unset
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetunset.php
	 * @param mixed $offset <p>
	 *                      The offset to unset.
	 *                      </p>
	 * @return void
	 */
	public function offsetUnset($offset) {
		$this->offsetSet($offset, NULL);
	}

	/**
	 * Returns the integer value of the given variable, or NULL if it could not be converted
	 *
	 * @param mixed $var
	 * @return int|NULL
	 */
	protected function _intValue($var){
		if (is_integer($var) || (string)(int)$var === $var) {
			return intval($var);
		}
		return NULL;
	}

} 