<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 03.11.14
 * Time: 20:53
 */

namespace Cundd\PersistentObjectStore\Index;

use Cundd\PersistentObjectStore\Domain\Model\DatabaseInterface;
use Cundd\PersistentObjectStore\Domain\Model\DocumentInterface;

/**
 * Interface for Index implementation
 *
 * @package Cundd\PersistentObjectStore\Index
 */
interface IndexInterface {
	/**
	 * Value not found in the Index
	 */
	const NOT_FOUND = -1;

	/**
	 * Some error occurred so do not trust this index
	 */
	const ERROR = -2;

	/**
	 * Returns if the Index is capable to lookup the given value
	 *
	 * @param mixed $value Value to lookup
	 * @return boolean
	 */
	public function canLookup($value);

	/**
	 * Looks up the given value and returns an array of the positions in the Database, NOT_FOUND if it was not found
	 * or ERROR if a problem was detected
	 *
	 * @param mixed $value Value to look for
	 * @return int[]
	 */
	public function lookup($value);

	/**
	 * Adds the given entry to the Index
	 *
	 * @param DocumentInterface|array $document
	 * @param int                     $position
	 * @return $this
	 */
	public function addEntryWithPosition($document, $position);

	/**
	 * Updates the given entry in the Index
	 *
	 * @param DocumentInterface|array $document
	 * @param int                     $position
	 * @return $this
	 */
	public function updateEntryForPosition($document, $position);

	/**
	 * Removes the given entry in the Index
	 *
	 * @param DocumentInterface|array $document
	 * @return $this
	 */
	public function deleteEntry($document);

	/**
	 * Builds the index for the given collection
	 *
	 * @param DatabaseInterface|\Iterator $database
	 * @return $this
	 */
	public function indexDatabase($database);

	/**
	 * Returns the property key to be indexed
	 *
	 * @return string
	 */
	public function getProperty();
} 