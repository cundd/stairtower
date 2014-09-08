<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 30.08.14
 * Time: 14:26
 */

namespace Cundd\PersistentObjectStore\Driver;


class Connection implements \Doctrine\DBAL\Driver\Connection {
	/**
	 * Prepares a statement for execution and returns a Statement object.
	 *
	 * @param string $prepareString
	 *
	 * @return \Doctrine\DBAL\Driver\Statement
	 */
	function prepare($prepareString) {
		// TODO: Implement prepare() method.
	}

	/**
	 * Executes an SQL statement, returning a result set as a Statement object.
	 *
	 * @return \Doctrine\DBAL\Driver\Statement
	 */
	function query() {
		// TODO: Implement query() method.
	}

	/**
	 * Quotes a string for use in a query.
	 *
	 * @param string  $input
	 * @param integer $type
	 *
	 * @return string
	 */
	function quote($input, $type = \PDO::PARAM_STR) {
		// TODO: Implement quote() method.
	}

	/**
	 * Executes an SQL statement and return the number of affected rows.
	 *
	 * @param string $statement
	 *
	 * @return integer
	 */
	function exec($statement) {
		// TODO: Implement exec() method.
	}

	/**
	 * Returns the ID of the last inserted row or sequence value.
	 *
	 * @param string|null $name
	 *
	 * @return string
	 */
	function lastInsertId($name = NULL) {
		// TODO: Implement lastInsertId() method.
	}

	/**
	 * Initiates a transaction.
	 *
	 * @return boolean TRUE on success or FALSE on failure.
	 */
	function beginTransaction() {
		// TODO: Implement beginTransaction() method.
	}

	/**
	 * Commits a transaction.
	 *
	 * @return boolean TRUE on success or FALSE on failure.
	 */
	function commit() {
		// TODO: Implement commit() method.
	}

	/**
	 * Rolls back the current transaction, as initiated by beginTransaction().
	 *
	 * @return boolean TRUE on success or FALSE on failure.
	 */
	function rollBack() {
		// TODO: Implement rollBack() method.
	}

	/**
	 * Returns the error code associated with the last operation on the database handle.
	 *
	 * @return string|null The error code, or null if no operation has been run on the database handle.
	 */
	function errorCode() {
		// TODO: Implement errorCode() method.
	}

	/**
	 * Returns extended error information associated with the last operation on the database handle.
	 *
	 * @return array
	 */
	function errorInfo() {
		// TODO: Implement errorInfo() method.
	}

} 