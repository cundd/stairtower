<?php
declare(strict_types=1);

namespace Cundd\Stairtower\DataAccess;

use Cundd\Stairtower\Domain\Model\DatabaseInterface;

/**
 * Class to write data to it's source
 */
interface WriterInterface
{
    /**
     * Write the given database to the disk
     *
     * @param DatabaseInterface $database
     * @throws Exception\WriterException if the data could not be written
     */
    public function writeDatabase(DatabaseInterface $database);

    /**
     * Creates a new database with the given identifier and options
     *
     * @param string $databaseIdentifier Unique identifier of the database
     * @param array  $options            Additional options for the created database
     */
    public function createDatabase(string $databaseIdentifier, array $options = []);

    /**
     * Deletes the database with the given identifier
     *
     * @param string $databaseIdentifier Unique identifier of the database
     * @return void
     */
    public function dropDatabase(string $databaseIdentifier);
}
