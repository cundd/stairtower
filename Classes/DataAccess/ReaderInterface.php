<?php
declare(strict_types=1);

namespace Cundd\Stairtower\DataAccess;

use Cundd\Stairtower\DataAccess\Exception\ReaderException;
use Cundd\Stairtower\Domain\Model\DatabaseInterface;


/**
 * Class to read data from it's source
 */
interface ReaderInterface
{
    /**
     * Loads the database with the given identifier
     *
     * @param string $databaseIdentifier
     * @param int    $memoryUsage Amount of memory used to load the data
     * @return DatabaseInterface
     */
    public function loadDatabase(string $databaseIdentifier, &$memoryUsage = null): DatabaseInterface;

    /**
     * Returns if a database with the given identifier exists
     *
     * @param string          $databaseIdentifier Unique identifier of the database
     * @param ReaderException $error              Reference to be filled with an exception describing the error if the database could not be read
     * @return bool
     */
    public function databaseExists(string $databaseIdentifier, &$error = null): bool;

    /**
     * Returns an array of the identifiers of databases that are already persisted
     *
     * @return string[]
     */
    public function listPersistedDatabases(): array;
}
