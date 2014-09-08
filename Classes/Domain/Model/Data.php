<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 15.08.14
 * Time: 20:12
 */

namespace Cundd\PersistentObjectStore\Domain\Model;
use Cundd\PersistentObjectStore\Utility\ObjectUtility;


/**
 * Class that represents a block of data
 *
 * @package Cundd\PersistentObjectStore
 */
class Data implements DataInterface {
	protected $creationTime;
	protected $modificationTime;
	protected $databaseIdentifier;
	protected $id;
	protected $data;


	/**
	 * Returns the global unique identifier
	 *
	 * @return string
	 */
	public function getGuid() {
		return $this->getDatabaseIdentifier() . '-' . $this->getId();
	}

	/**
	 * Returns the timestamp of the creation
	 *
	 * @return int
	 */
	public function getCreationTime() {
		return $this->creationTime;
	}

	/**
	 * Returns the timestamp of the creation
	 *
	 * @param int $creationTime
	 */
	public function setCreationTime($creationTime) {
		$this->creationTime = $creationTime;
	}


	/**
	 * Returns the timestamp of the last modification
	 *
	 * @return int
	 */
	public function getModificationTime() {
		return $this->modificationTime;
	}

	/**
	 * Returns the timestamp of the last modification
	 *
	 * @param int $modificationTime
	 */
	public function setModificationTime($modificationTime) {
		$this->modificationTime = $modificationTime;
	}


	/**
	 * Returns the associated database
	 *
	 * @return string
	 */
	public function getDatabaseIdentifier() {
		return $this->databaseIdentifier;
	}

	/**
	 * Returns the associated database
	 *
	 * @param string $databaseIdentifier
	 */
	public function setDatabaseIdentifier($databaseIdentifier) {
		$this->databaseIdentifier = $databaseIdentifier;
	}


	/**
	 * Returns the ID
	 *
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Returns the ID
	 *
	 * @param string $id
	 */
	public function setId($id) {
		$this->id = $id;
	}


	/**
	 * Returns the underlying data
	 *
	 * @return mixed
	 */
	public function getData() {
		return $this->data;
	}

	/**
	 * Returns the underlying data
	 *
	 * @param mixed $data
	 */
	public function setData($data) {
		$this->data = $data;
	}

	/**
	 * Returns the value for the given key from the data
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function valueForKey($key) {
		return ObjectUtility::valueForKeyPathOfObject($key, $this->getData());
	}

	/**
	 * Returns the value for the given key path from the data
	 *
	 * @param string $keyPath
	 * @return mixed
	 */
	public function valueForKeyPath($keyPath) {
		return ObjectUtility::valueForKeyPathOfObject($keyPath, $this->getData());
	}
}