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
use Cundd\PersistentObjectStore\Domain\Model\DatabaseInterface;
use Cundd\PersistentObjectStore\Domain\Model\DataInterface;
use Cundd\PersistentObjectStore\Serializer\JsonSerializer;
use Cundd\PersistentObjectStore\System\Lock\Factory;
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
	 * Write the given database to the disk
	 *
	 * @param DatabaseInterface $database
	 * @throws Exception\WriterException if the data could not be written
	 */
	public function writeDatabase($database) {
		$this->_prepareWriteDirectory();
		$databaseIdentifier = $database->getIdentifier();
		$path = $this->_getWriteDirectory() . $databaseIdentifier . '.json';

		$result = $this->_writeData($this->_getDataToWrite($database), $path, $databaseIdentifier);
		if ($result === FALSE) {
			throw new WriterException(
				sprintf(
					'Could not write data from database %s to file "%s"',
					$database->getIdentifier(),
					$path
				),
				1410291420
			);
		}
	}

	/**
	 * Creates a new database with the given identifier and options
	 *
	 * @param string $databaseIdentifier Unique identifier of the database
	 * @param array  $options Additional options for the created database
	 * @return DatabaseInterface
	 */
	public function createDatabase($databaseIdentifier, $options = array()) {
		$this->_prepareWriteDirectory();
		$path = $this->_getWriteDirectory() . $databaseIdentifier . '.json';

		if (file_exists($path)) throw new WriterException(sprintf('Database with identifier %s already exists', $databaseIdentifier), 1412509808);
		if (!is_writable($path) && !is_writable(dirname($path))) throw new WriterException(
			sprintf('No access to write the database with identifier %s to %s', $databaseIdentifier, $path),
			1412509809
		);

		$result = $this->_writeData('[]', $path, $databaseIdentifier);
		if ($result === FALSE) {
			throw new WriterException(
				sprintf(
					'Could not create database %s in file "%s"',
					$databaseIdentifier,
					$path
				),
				1410291420
			);
		}
	}

	/**
	 * Deletes the database with the given identifier
	 *
	 * @param string $databaseIdentifier Unique identifier of the database
	 * @return void
	 */
	public function dropDatabase($databaseIdentifier) {
		$path = $this->_getReadDirectory() . $databaseIdentifier . '.json';

		if (!file_exists($path)) throw new WriterException(sprintf('Database with identifier %s does not exist', $databaseIdentifier), 1412526598);
		if (!is_writable($path) && !is_writable(dirname($path))) throw new WriterException(
			sprintf('No access to write the database with identifier %s to %s', $databaseIdentifier, $path),
			1412526604
		);

		$fileHandle = @fopen($path, 'w+');
		if (!$fileHandle) {
			$error = error_get_last();
			throw new WriterException($error['message'], 1412526609);
		}

		$lock = $this->_getLockForDatabase($databaseIdentifier);
		if ($lock->tryLock()) { // acquire an exclusive lock
			ftruncate($fileHandle, 0); // truncate file
			fflush($fileHandle); // flush output before releasing the lock
			unlink($path);
			$lock->unlock(); // release the lock
		} else {
			throw new WriterException(
				sprintf(
					'Unable to acquire an exclusive lock for file "%s"',
					$path),
				1412526613
			);
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
	 * Returns the directory path to read data from
	 *
	 * @return string
	 */
	protected function _getReadDirectory() {
		return ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('dataPath');
	}

	/**
	 * Return a lock for the given database
	 *
	 * @param string $databaseIdentifier
	 * @return \Cundd\PersistentObjectStore\System\Lock\LockInterface
	 */
	protected function _getLockForDatabase($databaseIdentifier) {
		return Factory::createLock($databaseIdentifier);
	}

	/**
	 * Writes the data string to the file system
	 *
	 * @param string $data
	 * @param string $file
	 * @param string $databaseIdentifier
	 * @throws Exception\WriterException if the lock could not be acquired
	 * @return int Returns the number of bytes that were written to the file, or FALSE on failure
	 */
	protected function _writeData($data, $file, $databaseIdentifier) {
		$fileHandle = @fopen($file, 'w+');
		if (!$fileHandle) {
			$error = error_get_last();
			throw new WriterException($error['message'], 1410290532);
		}

		$lock = $this->_getLockForDatabase($databaseIdentifier);
		if ($lock->tryLock()) { // acquire an exclusive lock
			ftruncate($fileHandle, 0); // truncate file
			$bytesWritten = fwrite($fileHandle, $data);
			fflush($fileHandle); // flush output before releasing the lock
			$lock->unlock(); // release the lock
		} else {
			throw new WriterException(
				sprintf(
					'Unable to acquire an exclusive lock for file "%s"',
					$file),
				1410290540
			);
		}
		fclose($fileHandle);

		return $bytesWritten;
	}

	/**
	 * Prepares the directory for writing
	 *
	 * @throws Exception\WriterException if the folder exists but is not writable
	 */
	protected function _prepareWriteDirectory() {
		$writeFolder = $this->_getWriteDirectory();
		if (!file_exists($writeFolder)) {
			mkdir($writeFolder, 0774, TRUE);
		} else if (file_exists($writeFolder) && !is_writable($writeFolder)) {
			throw new WriterException('Document folder exists but is not writable', 1410188161);
		}
	}

	/**
	 * Returns the Document objects that will be written to the file system
	 *
	 * @param DatabaseInterface $database
	 * @return array
	 */
	protected function _getObjectsWrite($database) {
		$objectsToWrite = array();
		$database->rewind();
		while ($database->valid()) {
			/** @var DataInterface $item */
			$item = $database->current();
			if ($item) {
				$objectsToWrite[] = $item->getData();
//			} else {
//				DebugUtility::pl('Current item is NULL');
			}
			$database->next();
		}
		return $objectsToWrite;
	}

	/**
	 * Returns the string that will be written to the file system
	 *
	 * @param DatabaseInterface $database
	 * @return string
	 */
	protected function _getDataToWrite($database) {
		$objectsToWrite = $this->_getObjectsWrite($database);
		$serializer     = new JsonSerializer();
		return $serializer->serialize($objectsToWrite);
	}

//	/**
//	 * Loads the given meta database
//	 *
//	 * @param string $database
//	 * @return array<Document>
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