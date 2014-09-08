<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 15.08.14
 * Time: 20:11
 */

namespace Cundd\PersistentObjectStore\DataAccess;
use Cundd\PersistentObjectStore\Configuration\ConfigurationManager;
use Cundd\PersistentObjectStore\DataAccess\Exception\WriterException;
use Cundd\PersistentObjectStore\Domain\Model\Database;
use Cundd\PersistentObjectStore\Domain\Model\DataInterface;
use Cundd\PersistentObjectStore\Serializer\JsonSerializer;
use Cundd\PersistentObjectStore\Utility\DebugUtility;

/**
 * Class to write data to it's source
 *
 * @package Cundd\PersistentObjectStore\DataAccess
 */
class Writer {
	/**
	 * Used data encoding
	 */
	const DATA_ENCODING = 'json';

	/**
	 * Write the database with the given identifier
	 *
	 * @param Database $database
	 * @throws Exception\WriterException if the data could not be written
	 */
	public function writeDatabase($database) {
		$this->_prepareWriteDirectory();
		$path =  $this->_getWriteDirectory() . $database->getIdentifier() . '.json';

		DebugUtility::var_dump($path);

		$result = $this->_writeData($this->_getDataToWrite($database), $path);
		if ($result === FALSE) {
			throw new WriterException(sprintf(
				'Could not write data from database %s to file "%s"',
				$database->getIdentifier(),
				$path
			));
		}
	}

	/**
	 * Returns the directory path to write data in
	 *
	 * @return string
	 */
	protected function _getWriteDirectory() {
		return ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('writeDataPath');
	}

	/**
	 * Writes the data string to the file system
	 *
	 * @param string $data
	 * @param string $file
	 * @throws Exception\WriterException if the lock could not be acquired
	 * @return int Returns the number of bytes that were written to the file, or FALSE on failure
	 */
	protected function _writeData($data, $file) {
		$fileHandle = fopen($file, 'r+');

		if (flock($fileHandle, LOCK_EX)) {	// acquire an exclusive lock
			ftruncate($fileHandle, 0);		// truncate file
			$bytesWritten = fwrite($fileHandle, $data);
			fflush($fileHandle);			// flush output before releasing the lock
			flock($fileHandle, LOCK_UN);	// release the lock
		} else {
			throw new WriterException(sprintf(
					'Unable to acquire an exclusive lock for file "%s"',
					$file)
			);
		}
		fclose($fileHandle);

		return $bytesWritten;
	}

	/**
	 * Prepares the directory for writing
	 *
	 * @throws Exception\WriterException if the folder exists but is not writeable
	 */
	protected function _prepareWriteDirectory() {
		$writeFolder = $this->_getWriteDirectory();
		if (!file_exists($writeFolder)) {
			mkdir($writeFolder, 0774, TRUE);
		} else if (file_exists($writeFolder) && !is_writable($writeFolder)) {
			throw new WriterException('Data folder exists but is not writable', 1410188161);
		}
	}

	/**
	 * Returns the Data objects that will be written to the file system
	 *
	 * @param Database $database
	 * @return array
	 */
	protected function _getObjectsWrite($database) {
		$objectsToWrite = array();
		$database->rewind();
		while ($database->valid()) {
			/** @var DataInterface $item */
			$item = $database->current();

			$objectsToWrite[] = $item->getData();
			$database->next();
		}
		return $objectsToWrite;
	}

	/**
	 * Returns the string that will be written to the file system
	 *
	 * @param Database $database
	 * @return array
	 */
	protected function _getDataToWrite($database) {
		$objectsToWrite = $this->_getObjectsWrite($database);
		$serializer = new JsonSerializer();
		return $serializer->serialize($objectsToWrite);
	}

//	/**
//	 * Loads the given meta database
//	 *
//	 * @param string $database
//	 * @return array<Data>
//	 * @throws ReaderException if the database could not be found
//	 */
//	protected function _loadMetaDataCollection($database) {
//		$path = ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('dataPath') . $database . '.meta.json';
//		if (!file_exists($path)) {
//			return array();
//			throw new ReaderException("Database with name '$database' not found", 1408127629);
//		}
//
//		$serializer = new JsonSerializer();
//		return $serializer->unserialize(file_get_contents($path));
//	}
} 