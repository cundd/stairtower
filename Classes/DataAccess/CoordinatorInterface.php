<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 15.08.14
 * Time: 20:11
 */

namespace Cundd\PersistentObjectStore\DataAccess;

/**
 * Interface for coordinators responsible for managing the data
 *
 * @package Cundd\PersistentObjectStore\DataAccess
 */
interface CoordinatorInterface {
	/**
	 * Returns all data of the given database
	 *
	 * @param string $database
	 * @return array
	 */
	public function getDataByDatabase($database);

	/**
	 * Returns all data matching the given query
	 *
	 * @param $query
	 * @return array
	 */
	public function getDataByQuery($query);
}