<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 15.08.14
 * Time: 20:12
 */

namespace Cundd\PersistentObjectStore\Domain\Model;
use Cundd\PersistentObjectStore\KeyValueCodingInterface;

/**
 * Abstract interface to describe the parameters of a persisted object
 *
 * @package Cundd\PersistentObjectStore
 */
interface DataInterface extends KeyValueCodingInterface {
	/**
	 * Returns the timestamp of the creation
	 *
	 * @return int
	 */
	public function getCreationTime();

	/**
	 * Returns the timestamp of the last modification
	 *
	 * @return int
	 */
	public function getModificationTime();

	/**
	 * Returns the associated database
	 *
	 * @return string
	 */
	public function getDatabaseIdentifier();

	/**
	 * Returns the key for the identifier of the Data object
	 *
	 * @return string
	 */
	public function getIdentifierKey();

	/**
	 * Returns the global unique identifier
	 *
	 * @return string
	 */
	public function getGuid();

	/**
	 * Returns the ID
	 *
	 * @return string
	 */
	public function getId();

	/**
	 * Returns the underlying data
	 *
	 * @return mixed
	 */
	public function getData();

	/**
	 * Returns the value for the given key from the data
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function valueForKey($key);

	/**
	 * Sets the value for the given key from the data
	 *
	 * @param mixed $value
	 * @param string $key
	 */
	public function setValueForKey($value, $key);

	/**
	 * Returns the value for the given key path from the data
	 *
	 * @param string $keyPath
	 * @return mixed
	 */
	public function valueForKeyPath($keyPath);
}