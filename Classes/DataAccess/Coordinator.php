<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 15.08.14
 * Time: 20:11
 */

namespace Cundd\PersistentObjectStore\DataAccess;
use Cundd\PersistentObjectStore\Domain\Model\Exception\InvalidDatabaseException;
use Cundd\PersistentObjectStore\Domain\Model\Database;
use Cundd\PersistentObjectStore\Domain\Model\DatabaseInterface;
use Cundd\PersistentObjectStore\Memory\Manager;
use Cundd\PersistentObjectStore\Utility\GeneralUtility;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Coordinator responsible for managing the data
 *
 * @package Cundd\PersistentObjectStore\DataAccess
 */
class Coordinator implements CoordinatorInterface {
	const MEMORY_MANAGER_TAG = 'databases';

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
	 * @var \Evenement\EventEmitterInterface
	 * @Inject
	 */
	protected $eventEmitter;

	/**
	 * Array of databases and their objects
	 *
	 * @var array<array<mixed>>
	 */
	#protected $objectStore = array();

	/**
	 * Returns the database with the given identifier
	 *
	 * @param string $databaseIdentifier
	 * @return DatabaseInterface
	 */
	public function getDatabase($databaseIdentifier) {
		GeneralUtility::assertDatabaseIdentifier($databaseIdentifier);
		return $this->_getDatabase($databaseIdentifier);
	}

	/**
	 * Creates a new database with the given identifier and options
	 *
	 * @param string $databaseIdentifier Unique identifier of the database
	 * @param array  $options            Additional options for the created database
	 * @return DatabaseInterface
	 */
	public function createDatabase($databaseIdentifier, $options = array()) {
		GeneralUtility::assertDatabaseIdentifier($databaseIdentifier);
		if ($this->databaseExists($databaseIdentifier)) throw new InvalidDatabaseException(sprintf('Database "%s" already exists', $databaseIdentifier), 1412524749);
		if (Manager::hasObject($databaseIdentifier)) throw new InvalidDatabaseException(sprintf('Database "%s" already exists in memory', $databaseIdentifier), 1412524750);

		$this->dataWriter->createDatabase($databaseIdentifier, $options);
		$this->eventEmitter->emit(Event::DATABASE_CREATED, array($databaseIdentifier));

		$newDatabase = new Database($databaseIdentifier);
		Manager::registerObject($newDatabase, $databaseIdentifier, array(self::MEMORY_MANAGER_TAG));
		return $newDatabase;
	}

	/**
	 * Drops the database with the given identifier
	 *
	 * @param string $databaseIdentifier Unique identifier of the database
	 * @return void
	 */
	public function dropDatabase($databaseIdentifier) {
		GeneralUtility::assertDatabaseIdentifier($databaseIdentifier);

		// If the database is in the object store remove it
		if (Manager::hasObject($databaseIdentifier)) {
			Manager::free($databaseIdentifier);
		}
		if (!$this->databaseExists($databaseIdentifier)) throw new InvalidDatabaseException(sprintf('Database "%s" does not exist', $databaseIdentifier), 1412525836);

		$this->dataWriter->dropDatabase($databaseIdentifier);
		$this->eventEmitter->emit(Event::DATABASE_DROPPED, array($databaseIdentifier));
	}

	/**
	 * Returns if the database with the given identifier exists
	 *
	 * @param string $databaseIdentifier Unique identifier of the database
	 * @return bool
	 */
	public function databaseExists($databaseIdentifier) {
		GeneralUtility::assertDatabaseIdentifier($databaseIdentifier);
		if (Manager::hasObject($databaseIdentifier)) {
			return TRUE;
		}
		return $this->dataReader->databaseExists($databaseIdentifier);
	}

	/**
	 * Returns an array of the identifiers of available databases
	 *
	 * @return array
	 */
	public function listDatabases() {
		$persistedDatabases = $this->listPersistedDatabases();
		$persistedDatabases = array_combine($persistedDatabases, $persistedDatabases);

		$inMemoryDatabases = Manager::getIdentifiersByTag(self::MEMORY_MANAGER_TAG, TRUE);
		$inMemoryDatabases = array_combine($inMemoryDatabases, $inMemoryDatabases);

		$allDatabases = array_merge($persistedDatabases, $inMemoryDatabases);
		return array_keys($allDatabases);
	}

	/**
	 * Returns an array of the identifiers of databases that are not already persisted
	 *
	 * @return array<string>
	 */
	public function listInMemoryDatabases() {
		return array_diff($this->listDatabases(), $this->listPersistedDatabases());
	}

	/**
	 * Returns an array of the identifiers of databases that are already persisted
	 *
	 * @return array<string>
	 */
	public function listPersistedDatabases() {
		return $this->dataReader->listPersistedDatabases();
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
	 * @param DatabaseInterface $database
	 */
	public function commitDatabase($database) {
		$this->dataWriter->writeDatabase($database);
		$this->eventEmitter->emit(Event::DATABASE_COMMITTED, array($database));
	}

	/**
	 * Commit all changed databases to the file system
	 */
	public function commitDatabases() {
		foreach (Manager::getObjectsByTag(self::MEMORY_MANAGER_TAG) as $database) {
			$this->commitDatabase($database);
		}
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
		if (!Manager::hasObject($databaseIdentifier)) {
			$memoryUsage = NULL;
			$database = $this->dataReader->loadDatabase($databaseIdentifier, $memoryUsage);
			Manager::registerObject($database, $databaseIdentifier, array(self::MEMORY_MANAGER_TAG));
			return $database;
		}
		return Manager::getObject($databaseIdentifier);
	}

	/**
	 * Returns the static object store
	 *
	 * @return array
	 * @internal
	 */
	public function getObjectStore() {
		return Manager::getObjectsByTag(self::MEMORY_MANAGER_TAG);
	}
}