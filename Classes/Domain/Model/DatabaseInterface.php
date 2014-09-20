<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 20.09.14
 * Time: 10:44
 */

namespace Cundd\PersistentObjectStore\Domain\Model;


interface DatabaseInterface {
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
} 