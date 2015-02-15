<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 15.08.14
 * Time: 20:11
 */

namespace Cundd\PersistentObjectStore\DataAccess;

use Cundd\PersistentObjectStore\Domain\Model\Database;
use Cundd\PersistentObjectStore\Domain\Model\DatabaseInterface;
use Cundd\PersistentObjectStore\Domain\Model\DatabaseStateInterface;
use Cundd\PersistentObjectStore\Domain\Model\Exception\InvalidDatabaseException;
use Cundd\PersistentObjectStore\Memory\Manager;
use Cundd\PersistentObjectStore\Utility\GeneralUtility;

/**
 * Coordinator responsible for managing the data
 *
 * @package Cundd\PersistentObjectStore\DataAccess
 */
class Coordinator implements CoordinatorInterface
{
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
     * @var \Evenement\EventEmitterInterface
     * @Inject
     */
    protected $eventEmitter;

    /**
     * @var \Psr\Log\LoggerInterface
     * @inject
     */
    protected $logger;

    /**
     * Array of databases
     *
     * @var string[]
     */
    protected $allDatabaseIdentifiers = null;

    /**
     * Array of databases and their objects
     *
     * @var array[]
     */
    #protected $objectStore = array();


    /**
     * Returns the database with the given identifier
     *
     * @param string $databaseIdentifier
     * @return DatabaseInterface
     */
    public function getDatabase($databaseIdentifier)
    {
        GeneralUtility::assertDatabaseIdentifier($databaseIdentifier);
        if (!Manager::hasObject($databaseIdentifier)) {
            $memoryUsage = null;
            $database    = $this->dataReader->loadDatabase($databaseIdentifier, $memoryUsage);
            Manager::registerObject($database, $databaseIdentifier, array(self::MEMORY_MANAGER_TAG));
            return $database;
        }
        return Manager::getObject($databaseIdentifier);
    }

    /**
     * Creates a new database with the given identifier and options
     *
     * @param string $databaseIdentifier Unique identifier of the database
     * @param array  $options            Additional options for the created database
     * @return DatabaseInterface
     */
    public function createDatabase($databaseIdentifier, $options = array())
    {
        GeneralUtility::assertDatabaseIdentifier($databaseIdentifier);
        if ($this->databaseExists($databaseIdentifier)) {
            throw new InvalidDatabaseException(sprintf('Database "%s" already exists', $databaseIdentifier),
                1412524749);
        }
        if (Manager::hasObject($databaseIdentifier)) {
            throw new InvalidDatabaseException(sprintf('Database "%s" already exists in memory', $databaseIdentifier),
                1412524750);
        }

        $this->dataWriter->createDatabase($databaseIdentifier, $options);

        $newDatabase = new Database($databaseIdentifier);
        Manager::registerObject($newDatabase, $databaseIdentifier, array(self::MEMORY_MANAGER_TAG));
        $this->allDatabaseIdentifiers[$databaseIdentifier] = $databaseIdentifier;
        $this->logger->info(sprintf('Create database "%s"', $databaseIdentifier));
        $this->eventEmitter->emit(Event::DATABASE_CREATED, array($databaseIdentifier));
        return $newDatabase;
    }

    /**
     * Returns if the database with the given identifier exists
     *
     * @param string $databaseIdentifier Unique identifier of the database
     * @return bool
     */
    public function databaseExists($databaseIdentifier)
    {
        GeneralUtility::assertDatabaseIdentifier($databaseIdentifier);
        if ($this->allDatabaseIdentifiers === null) {
            $this->listDatabases();
        }
        return isset($this->allDatabaseIdentifiers[$databaseIdentifier]);
    }

    /**
     * Drops the database with the given identifier
     *
     * @param string $databaseIdentifier Unique identifier of the database
     * @return void
     */
    public function dropDatabase($databaseIdentifier)
    {
        GeneralUtility::assertDatabaseIdentifier($databaseIdentifier);

        // If the database is in the object store remove it
        if (Manager::hasObject($databaseIdentifier)) {
            Manager::free($databaseIdentifier);
        }
        if (!$this->databaseExists($databaseIdentifier)) {
            throw new InvalidDatabaseException(sprintf('Database "%s" does not exist', $databaseIdentifier),
                1412525836);
        }

        $this->dataWriter->dropDatabase($databaseIdentifier);
        unset($this->allDatabaseIdentifiers[$databaseIdentifier]);
        $this->logger->info(sprintf('Drop database "%s"', $databaseIdentifier));
        $this->eventEmitter->emit(Event::DATABASE_DROPPED, array($databaseIdentifier));
    }

    /**
     * Returns an array of the identifiers of databases that are not already persisted
     *
     * @return string[]
     */
    public function listInMemoryDatabases()
    {
        return array_diff($this->listDatabases(), $this->listPersistedDatabases());
    }

    /**
     * Returns an array of the identifiers of available databases
     *
     * @return array
     */
    public function listDatabases()
    {
        if ($this->allDatabaseIdentifiers === null) {
            $this->allDatabaseIdentifiers = array_combine(
                $this->listPersistedDatabases(),
                $this->listPersistedDatabases()
            );
        }
        return array_values($this->allDatabaseIdentifiers);
    }

    /**
     * Returns an array of the identifiers of databases that are already persisted
     *
     * @return string[]
     */
    public function listPersistedDatabases()
    {
        return $this->dataReader->listPersistedDatabases();
    }

    /**
     * Returns all data matching the given query
     *
     * @param $query
     * @return array
     */
    public function getDataByQuery($query)
    {
    }

    /**
     * Commit all changed databases to the file system
     */
    public function commitDatabases()
    {
        $databases = Manager::getObjectsByTag(self::MEMORY_MANAGER_TAG);
        if ($databases) {
            $this->logger->debug(sprintf('Number of databases to commit: %d', count($databases)));

            /** @var DatabaseInterface $database */
            foreach ($databases as $database) {
                if ($database->getState() === DatabaseStateInterface::STATE_DIRTY) {
                    $this->commitDatabase($database);
                }
            }
        } else {
            $this->logger->debug('No databases to commit');
        }
    }

    /**
     * Commit the database to the file system
     *
     * @param DatabaseInterface $database
     */
    public function commitDatabase($database)
    {
        $this->logger->info(sprintf('Commit database "%s"', $database->getIdentifier()));
        $this->dataWriter->writeDatabase($database);
        $database->setState(DatabaseStateInterface::STATE_CLEAN);
        $this->eventEmitter->emit(Event::DATABASE_COMMITTED, array($database));
    }

    /**
     * Performs the query from the given query parts on the database
     *
     * @param array    $queryParts
     * @param Database $database
     * @return array Returns the result
     */
    protected function performQueryOnDatabase($queryParts, $database)
    {
//		if (isset($queryParts['select']))
    }
}