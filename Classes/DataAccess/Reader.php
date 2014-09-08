<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 15.08.14
 * Time: 20:10
 */

namespace Cundd\PersistentObjectStore\DataAccess;
use Cundd\PersistentObjectStore\Configuration\ConfigurationManager;
use Cundd\PersistentObjectStore\Domain\Model\Data;
use Cundd\PersistentObjectStore\DataAccess\Exception\ReaderException;
use Cundd\PersistentObjectStore\Domain\Model\Database;
use Cundd\PersistentObjectStore\Serializer\JsonSerializer;

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
	 * @return Database
	 * @throws ReaderException if the database could not be found
	 */
	public function loadDatabase($databaseIdentifier) {
		$database = new Database($databaseIdentifier);
		$dataCollection = $this->_loadDataCollection($databaseIdentifier);
		$metaDataCollection = $this->_loadMetaDataCollection($databaseIdentifier);
		$this->_fillDatabaseWithData($database, $dataCollection, $metaDataCollection);
		return $database;
	}

	/**
	 * Fills the database with the given data
	 *
	 * @param Database $database
	 * @param array<Data> $data
	 * @param array<Data> $metaData
	 */
	protected function _fillDatabaseWithData($database, $dataCollection, $metaDataCollection) {
		$databaseIdentifier = $database->getIdentifier();
		$rawData = reset($dataCollection);
		do {
			$rawMetaData = reset($metaDataCollection);
			$dataObject = new Data();
			$dataObject->setData($rawData);

			$dataObject->setDatabaseIdentifier($databaseIdentifier);
			$dataObject->setId(isset($rawMetaData['id']) ? $rawMetaData['id'] : NULL);
			$dataObject->setCreationTime(isset($rawMetaData['creation_time']) ? $rawMetaData['creation_time'] : NULL);
			$dataObject->setModificationTime(isset($rawMetaData['modification_time']) ? $rawMetaData['modification_time'] : NULL);

			$database->attach($dataObject);
		} while ($rawData = next($dataCollection));
	}

	/**
	 * Loads the given raw database
	 *
	 * @param string $database
	 * @return array<Data>
	 * @throws ReaderException if the database could not be found
	 */
	protected function _loadDataCollection($database) {
		$path = ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('dataPath') . $database . '.json';
		if (!file_exists($path)) {
			throw new ReaderException("Database with name '$database' not found", 1408127629);
		}

		$serializer = new JsonSerializer();
		return $serializer->unserialize(file_get_contents($path));
	}

	/**
	 * Loads the given meta database
	 *
	 * @param string $database
	 * @return array<Data>
	 * @throws ReaderException if the database could not be found
	 */
	protected function _loadMetaDataCollection($database) {
		$path = ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('dataPath') . $database . '.meta.json';
		if (!file_exists($path)) {
			return array();
			throw new ReaderException("Database with name '$database' not found", 1408127629);
		}

		$serializer = new JsonSerializer();
		return $serializer->unserialize(file_get_contents($path));
	}
}