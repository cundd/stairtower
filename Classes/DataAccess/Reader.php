<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 15.08.14
 * Time: 20:10
 */

namespace Cundd\PersistentObjectStore\DataAccess;
use Cundd\PersistentObjectStore\Configuration\ConfigurationManager;
use Cundd\PersistentObjectStore\DataAccess\Exception\ReaderException;
use Cundd\PersistentObjectStore\Domain\Model\Database;
use Cundd\PersistentObjectStore\Memory\Helper;
use Cundd\PersistentObjectStore\Serializer\JsonSerializer;
use Cundd\PersistentObjectStore\System\Lock\Factory;

/**
 * Class to read data from it's source
 *
 * @package Cundd\PersistentObjectStore\DataAccess
 */
class Reader {
	/**
	 * Used data encoding
	 */
	const DATA_ENCODING = 'json';

	/**
	 * Loads the database with the given identifier
	 *
	 * @param string $databaseIdentifier
	 * @param int   $memoryUsage Amount of memory used to load the data
	 * @return Database
	 */
	public function loadDatabase($databaseIdentifier, &$memoryUsage = NULL) {
		$memoryUsage = memory_get_usage(TRUE);
		$database = new Database($databaseIdentifier);
		$dataCollection = $this->_loadDataCollection($databaseIdentifier);
		$metaDataCollection = $this->_loadMetaDataCollection($databaseIdentifier);
		$this->_fillDatabaseWithData($database, $dataCollection, $metaDataCollection);
		$memoryUsage = memory_get_usage(TRUE) - $memoryUsage;
		return $database;
	}

	/**
	 * Fills the database with the given data
	 *
	 * @param Database $database
	 * @param array<Document> $data
	 * @param array<Document> $metaData
	 */
	protected function _fillDatabaseWithData($database, $dataCollection, $metaDataCollection) {
		$database->setRawData($dataCollection);
	}

	/**
	 * Loads the given raw database
	 *
	 * @param string $databaseIdentifier
	 * @return array<Document>
	 * @throws ReaderException if the database could not be found
	 */
	protected function _loadDataCollection($databaseIdentifier) {
		$path = ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('dataPath') . $databaseIdentifier . '.json';
		$error = NULL;
		$this->databaseExists($databaseIdentifier, $error);
		if ($error instanceof ReaderException) throw $error;

		$memoryHelper = new Helper();
		$memoryHelper->checkMemoryForJsonFile($path);

//		DebugUtility::printMemorySample();
		$lock = Factory::createLock($databaseIdentifier);
		$lock->lockWithTimeout(20000);
		$fileData = file_get_contents($path);
		$lock->unlock();
//		DebugUtility::printMemorySample();
		$serializer = new JsonSerializer();
		$dataCollection = $serializer->unserialize($fileData);
//		DebugUtility::printMemorySample();

		return $dataCollection;
	}

	/**
	 * Returns if a database with the given identifier exists
	 *
	 * @param string $databaseIdentifier Unique identifier of the database
	 * @param ReaderException $error Reference to be filled with an exception describing the error if the database could not be read
	 * @return bool
	 */
	public function databaseExists($databaseIdentifier, &$error = NULL) {
		$path = ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('dataPath') . $databaseIdentifier . '.json';
		if (!file_exists($path)) {
			$error = new ReaderException("Database with name '$databaseIdentifier' not found", 1408127629);
			return FALSE;
		}
		if (!is_readable($path)) {
			$error = new ReaderException("Database with name '$databaseIdentifier' is not readable", 1412509416);
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Returns an array of the identifiers of databases that are already persisted
	 *
	 * @return array<string>
	 */
	public function listPersistedDatabases() {
		$foundDatabases = glob(ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('dataPath') . '*.json', GLOB_MARK);
		$foundDatabases = array_filter($foundDatabases, function($item) {
			return substr($item, -1) !== DIRECTORY_SEPARATOR;
		});
		$foundDatabases = array_map(function($item) {
			// Get the basename and strip '.json'
			return substr(basename($item), 0, -5);
		}, $foundDatabases);
		return $foundDatabases;
	}

	/**
	 * Loads the given meta database
	 *
	 * @param string $databaseIdentifier
	 * @return array<Document>
	 */
	protected function _loadMetaDataCollection($databaseIdentifier) {
		$path = ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('dataPath') . $databaseIdentifier . '.meta.json';
		if (!file_exists($path)) {
			return array();
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
}