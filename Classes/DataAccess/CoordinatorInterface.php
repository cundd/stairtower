<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\DataAccess;

use Cundd\PersistentObjectStore\Domain\Model\DatabaseInterface;

/**
 * Interface for coordinators responsible for managing the data
 */
interface CoordinatorInterface
{
    /**
     * Returns the database with the given identifier
     *
     * @param string $databaseIdentifier
     * @return DatabaseInterface
     */
    public function getDatabase(string $databaseIdentifier): DatabaseInterface;

    /**
     * Creates a new database with the given identifier and options
     *
     * @param string $databaseIdentifier Unique identifier of the database
     * @param array  $options            Additional options for the created database
     * @return DatabaseInterface
     */
    public function createDatabase(string $databaseIdentifier, $options = []): DatabaseInterface;

    /**
     * Drops the database with the given identifier
     *
     * @param string $databaseIdentifier Unique identifier of the database
     * @return void
     */
    public function dropDatabase(string $databaseIdentifier);

    /**
     * Returns if the database with the given identifier exists
     *
     * @param string $databaseIdentifier Unique identifier of the database
     * @return bool
     */
    public function databaseExists(string $databaseIdentifier): bool;

    /**
     * Returns an array of the identifiers of available databases
     *
     * @return array
     */
    public function listDatabases(): array;

    /**
     * Returns an array of the identifiers of databases that are not already persisted
     *
     * @return string[]
     */
    public function listInMemoryDatabases(): array;

    /**
     * Returns an array of the identifiers of databases that are already persisted
     *
     * @return string[]
     */
    public function listPersistedDatabases() : array;

    /**
     * Returns all data matching the given query
     *
     * @param $query
     * @return array
     */
    public function getDataByQuery($query):array;

    /**
     * Commit the database to the file system
     *
     * @param DatabaseInterface $database
     */
    public function commitDatabase(DatabaseInterface $database);

    /**
     * Commit all changed databases to the file system
     */
    public function commitDatabases();
}