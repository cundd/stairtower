<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 15.08.14
 * Time: 20:11
 */

namespace Cundd\PersistentObjectStore\DataAccess;
use Cundd\PersistentObjectStore\Domain\Model\Database;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Coordinator responsible for managing the data
 *
 * @package Cundd\PersistentObjectStore\DataAccess
 */
class Coordinator implements CoordinatorInterface {
	/**
	 * @var \Cundd\PersistentObjectStore\DataAccess\Reader
	 * @Inject
	 */
	protected $dataReader;

	/**
	 * @var \Cundd\PersistentObjectStore\DataAccess\Writer
	 * @Inject
	 */
	protected $dataWriter;

	/**
	 * @var \Cundd\PersistentObjectStore\DataAccess\ObjectFinderInterface
	 * @Inject
	 */
	protected $objectFinder;

	/**
	 * Array of databases and their objects
	 *
	 * @var array<array<mixed>>
	 */
	protected $objectStore = array();

	/**
	 * Returns all data of the given database
	 *
	 * @param string $database
	 * @return Database|array
	 */
	public function getDatabase($database) {
		return $this->_getDatabase($database);
	}

	/**
	 * Returns all data matching the given query
	 *
	 * @param $query
	 * @return array
	 */
	public function getDataByQuery($query) {
		$result = array();
		$queryParts = array();
		$parameters = array();

		$type = QueryConstants::SELECT;
		if (class_exists('Doctrine\\DBAL\\Query\\QueryBuilder') && $query instanceof \Doctrine\DBAL\Query\QueryBuilder) {
			$queryParts = $query->getQueryParts();
			$parameters = $query->getParameters();
			switch ($query->getType()) {
				case 1:
					$type = QueryConstants::DELETE;
					break;

				case 2:
					$type = QueryConstants::INSERT;
					break;

				case 0:
				default:
					$type = QueryConstants::SELECT;
			}
		}

		// Select to correct database (=table)
		$databaseDefinition = reset($queryParts['from']);
		$databaseIdentifier = $databaseDefinition['table'];
		$database = $this->_getDatabase($databaseIdentifier);

		if ($type == QueryConstants::SELECT) {
			$result = $this->_performSearchQueryOnDatabase($queryParts, $parameters, $database);
		}



		return $result;
	}

	/**
	 * Commit the database to the file system
	 *
	 * @param Database $database
	 */
	public function commitDatabase($database) {
		$this->dataWriter->writeDatabase($database);
	}

	/**
	 * Performs the query from the given query parts on the database
	 *
	 * @param array $queryParts
	 * @param Database $database
	 * @return array Returns the result
	 */
	protected function _performQueryOnDatabase($queryParts, $database) {
//		if (isset($queryParts['select']))
	}

	/**
	 * Performs the search query from the given query parts on the database
	 *
	 * @param array $queryParts Dictionary describing the query
	 * @param array $parameters Map of parameters and names
	 * @param Database $database
	 * @return array Returns the result
	 */
	protected function _performSearchQueryOnDatabase($queryParts, $parameters, $database) {
		if (isset($queryParts['where']) && $queryParts['where']) {
			$where = $queryParts['where'];
			$this->objectFinder->setConstraints($where);
			return $this->objectFinder->findInDatabase($database);
		}
		return $database;
	}

	/**
	 * Returns the database with the given identifier
	 *
	 * @param string $databaseIdentifier
	 * @return Database
	 */
	protected function _getDatabase($databaseIdentifier) {
		if (!isset($this->objectStore[$databaseIdentifier])) {
			$this->objectStore[$databaseIdentifier] = $this->dataReader->loadDatabase($databaseIdentifier);
		}
		return $this->objectStore[$databaseIdentifier];
	}
} 