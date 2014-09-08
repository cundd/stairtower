<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 30.08.14
 * Time: 14:23
 */

namespace Cundd\PersistentObjectStore\Driver;


use Doctrine\DBAL\Connection;

class Driver implements \Doctrine\DBAL\Driver {
	/**
	 * Attempts to create a connection with the database.
	 *
	 * @param array       $params        All connection parameters passed by the user.
	 * @param string|null $username      The username to use when connecting.
	 * @param string|null $password      The password to use when connecting.
	 * @param array       $driverOptions The driver options to use when connecting.
	 *
	 * @return \Doctrine\DBAL\Driver\Connection The database connection.
	 */
	public function connect(array $params, $username = NULL, $password = NULL, array $driverOptions = array()) {
		// TODO: Implement connect() method.
	}

	/**
	 * Gets the DatabasePlatform instance that provides all the metadata about
	 * the platform this driver connects to.
	 *
	 * @return \Doctrine\DBAL\Platforms\AbstractPlatform The database platform.
	 */
	public function getDatabasePlatform() {
		// TODO: Implement getDatabasePlatform() method.
	}

	/**
	 * Gets the SchemaManager that can be used to inspect and change the underlying
	 * database schema of the platform this driver connects to.
	 *
	 * @param \Doctrine\DBAL\Connection $conn
	 *
	 * @return \Doctrine\DBAL\Schema\AbstractSchemaManager
	 */
	public function getSchemaManager(Connection $conn) {
		// TODO: Implement getSchemaManager() method.
	}

	/**
	 * Gets the name of the driver.
	 *
	 * @return string The name of the driver.
	 */
	public function getName() {
		// TODO: Implement getName() method.
	}

	/**
	 * Gets the name of the database connected to for this driver.
	 *
	 * @param \Doctrine\DBAL\Connection $conn
	 *
	 * @return string The name of the database.
	 */
	public function getDatabase(Connection $conn) {
		// TODO: Implement getDatabase() method.
	}

} 