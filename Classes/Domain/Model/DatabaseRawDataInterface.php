<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 20.09.14
 * Time: 10:44
 */

namespace Cundd\PersistentObjectStore\Domain\Model;

use Cundd\PersistentObjectStore\Core\ArrayException\IndexOutOfRangeException;
use Cundd\PersistentObjectStore\Filter\Exception\InvalidCollectionException;

/**
 * Special database interface that describes the access to raw data
 *
 * @package Cundd\PersistentObjectStore\Domain\Model
 */
interface DatabaseRawDataInterface {
	/**
	 * Sets the raw data
	 *
	 * @param \SplFixedArray|array|\Iterator $rawData
	 * @throws InvalidCollectionException if the given data can not be used
	 * @internal
	 */
	public function setRawData($rawData);

	/**
	 * Returns the raw data
	 *
	 * @return \SplFixedArray
	 * @internal
	 */
	public function getRawData();

	/**
	 * Returns the current raw data
	 *
	 * @return mixed Can return any type
	 * @throws IndexOutOfRangeException if the current index is out of range
	 */
	public function currentRaw();
} 