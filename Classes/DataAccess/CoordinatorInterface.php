<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 15.08.14
 * Time: 20:11
 */

namespace Cundd\PersistentObjectStore\DataAccess;
use Cundd\PersistentObjectStore\Domain\Model\Database;

/**
 * Interface for coordinators responsible for managing the data
 *
 * @package Cundd\PersistentObjectStore\DataAccess
 */
interface CoordinatorInterface {
	/**
	 * Returns the database with the given identifier
	 *
	 * @param string $databaseIdentifier
	 * @return Database
	 */
	public function getDatabase($databaseIdentifier);

	/**
	 * Creates a new database with the given identifier and options
	 *
	 * @param string $databaseIdentifier Unique identifier of the database
	 * @param array  $options Additional options for the created database
	 * @return Database
	 */
	public function createDatabase($databaseIdentifier, $options = array());

	/**
	 * Drops the database with the given identifier
	 *
	 * @param string $databaseIdentifier Unique identifier of the database
	 * @return void
	 */
	public function dropDatabase($databaseIdentifier);

	/**
	 * Returns if the database with the given identifier exists
	 *
	 * @param string $databaseIdentifier Unique identifier of the database
	 * @return bool
	 */
	public function databaseExists($databaseIdentifier);

	/**
	 * Returns an array of the identifiers of available databases
	 *
	 * @return array
	 */
	public function listDatabases();

	/**
	 * Returns an array of the identifiers of databases that are not already persisted
	 *
	 * @return array<string>
	 */
	public function listInMemoryDatabases();

	/**
	 * Returns an array of the identifiers of databases that are already persisted
	 *
	 * @return array<string>
	 */
	public function listPersistedDatabases();

	/**
	 * Returns all data matching the given query
	 *
	 * @param $query
	 * @return array
	 */
	public function getDataByQuery($query);

	/**
	 * Commit the database to the file system
	 *
	 * @param Database $database
	 */
	public function commitDatabase($database);
}