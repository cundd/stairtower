<?php
declare(strict_types=1);

namespace Cundd\Stairtower\DataAccess;

use Cundd\Stairtower\Configuration\ConfigurationManager;
use Cundd\Stairtower\DataAccess\Exception\ReaderException;
use Cundd\Stairtower\Domain\Model\Database;
use Cundd\Stairtower\Domain\Model\DatabaseInterface;
use Cundd\Stairtower\Domain\Model\DatabaseStateInterface;
use Cundd\Stairtower\Domain\Model\DocumentInterface;
use Cundd\Stairtower\Serializer\JsonSerializer;
use Cundd\Stairtower\System\Lock\Factory;

/**
 * Class to read data from it's source
 */
class Reader
{
    /**
     * Used data encoding
     */
    const DATA_ENCODING = 'json';

    /**
     * @var \Cundd\Stairtower\Memory\Helper
     * @Inject
     */
    protected $memoryHelper;

    /**
     * Loads the database with the given identifier
     *
     * @param string $databaseIdentifier
     * @param int    $memoryUsage Amount of memory used to load the data
     * @return DatabaseInterface
     */
    public function loadDatabase(string $databaseIdentifier, &$memoryUsage = null): DatabaseInterface
    {
        $memoryUsage = memory_get_usage(true);
        $database = new Database($databaseIdentifier);
        $dataCollection = $this->loadDataCollection($databaseIdentifier);
        $metaDataCollection = $this->loadMetaDataCollection($databaseIdentifier);
        $this->fillDatabaseWithData($database, $dataCollection, $metaDataCollection);
        $memoryUsage = memory_get_usage(true) - $memoryUsage;

        return $database;
    }

    /**
     * Loads the given raw database
     *
     * @param string $databaseIdentifier
     * @return array
     * @throws ReaderException if the database could not be found
     */
    protected function loadDataCollection(string $databaseIdentifier)
    {
        $path = ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath(
                'dataPath'
            ) . $databaseIdentifier . '.json';
        $error = null;
        $this->databaseExists($databaseIdentifier, $error);
        if ($error instanceof ReaderException) {
            throw $error;
        }

        $this->memoryHelper->checkMemoryForJsonFile($path);

//		DebugUtility::printMemorySample();
        $lock = Factory::createLock($databaseIdentifier);
        $lock->lockWithTimeout(20000);
        $fileData = file_get_contents($path);
        $lock->unlock();
//		DebugUtility::printMemorySample();
        $serializer = new JsonSerializer();
        $dataCollection = $serializer->unserialize($fileData);
//		DebugUtility::printMemorySample();

        if ($dataCollection === null) {
            return [];
        }

        return $dataCollection;
    }

    /**
     * Returns if a database with the given identifier exists
     *
     * @param string          $databaseIdentifier Unique identifier of the database
     * @param ReaderException $error              Reference to be filled with an exception describing the error if the database could not be read
     * @return bool
     */
    public function databaseExists(string $databaseIdentifier, &$error = null): bool
    {
        $path = ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('dataPath')
            . $databaseIdentifier . '.json';
        if (!file_exists($path)) {
            $error = new ReaderException("Database with name '$databaseIdentifier' not found", 1408127629);

            return false;
        }
        if (!is_readable($path)) {
            $error = new ReaderException("Database with name '$databaseIdentifier' is not readable", 1412509416);

            return false;
        }
        //if (filesize($path) === 0) {
        //    $error = new ReaderException("Database with name '$databaseIdentifier' is an empty file", 1412509417);
        //    return false;
        //}
        return true;
    }

    /**
     * Returns an array of the identifiers of databases that are already persisted
     *
     * @return string[]
     */
    public function listPersistedDatabases(): array
    {
        $foundDatabases = glob(
            ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('dataPath') . '*.json',
            GLOB_MARK
        );
        $foundDatabases = array_filter(
            $foundDatabases,
            function ($item) {
                return substr($item, -1) !== DIRECTORY_SEPARATOR;
            }
        );
        $foundDatabases = array_map(
            function ($item) {
                // Get the basename and strip '.json'
                return substr(basename($item), 0, -5);
            },
            $foundDatabases
        );

        return $foundDatabases;
    }

    /**
     * Loads the given meta database
     *
     * @param string $databaseIdentifier
     * @return array
     */
    protected function loadMetaDataCollection(string $databaseIdentifier)
    {
        $path = ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('dataPath')
            . $databaseIdentifier . '.meta.json';
        if (!file_exists($path)) {
            return [];
        }

        $lock = Factory::createLock($databaseIdentifier);
        $lock->lock();
        $fileData = file_get_contents($path);
        $lock->unlock();
//		DebugUtility::printMemorySample();
        $serializer = new JsonSerializer();
        $dataCollection = $serializer->unserialize($fileData);

//		DebugUtility::printMemorySample();

        return $dataCollection;
    }

    /**
     * Fills the database with the given data
     *
     * @param Database            $database
     * @param DocumentInterface[] $dataCollection
     * @param DocumentInterface[] $metaDataCollection
     */
    protected function fillDatabaseWithData(
        Database $database,
        array $dataCollection,
        array $metaDataCollection
    ) {
        $database->setRawData($dataCollection);
        $database->setState(DatabaseStateInterface::STATE_CLEAN);
    }
}