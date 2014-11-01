<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 20.09.14
 * Time: 10:44
 */

namespace Cundd\PersistentObjectStore\Domain\Model;


interface DatabaseInterface extends \Iterator, \Countable, \SeekableIterator {
	/**
	 * Returns the database identifier
	 *
	 * @return string
	 */
	public function getIdentifier();

	/**
	 * Filters the database using the given comparisons
	 *
	 * @param array $comparisons
	 * @return \Cundd\PersistentObjectStore\Filter\FilterResultInterface
	 */
	public function filter($comparisons);

	/**
	 * Returns the object with the given identifier
	 *
	 * @param string $identifier
	 * @return DataInterface|NULL
	 */
	public function findByIdentifier($identifier);

	/**
	 * Sets the raw data
	 *
	 * [Optional]
	 * @param $rawData
	 */
	// public function setRawData($rawData);


	// MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
	// MANAGING OBJECTS
	// MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
	/**
	 * Adds the given data instance to the database
	 *
	 * @param DataInterface $dataInstance
	 */
	public function add($dataInstance);

	/**
	 * Updates the given data instance in the database
	 *
	 * @param DataInterface $dataInstance
	 */
	public function update($dataInstance);

	/**
	 * Removes the given data instance from the database
	 *
	 * @param DataInterface $dataInstance
	 */
	public function remove($dataInstance);

	/**
	 * Returns if the database contains the given data instance
	 *
	 * @param DataInterface|string $dataInstance Actual Document instance or it's GUID
	 * @return boolean
	 */
	public function contains($dataInstance);

} 