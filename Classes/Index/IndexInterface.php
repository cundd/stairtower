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
interface IndexInterface
{
    /**
     * Value was not found in the Index and thus does not exist in the collection covered by this Index
     */
    const NOT_FOUND = -1;

    /**
     * This Index can not provide a result for the lookup
     */
    const NO_RESULT = -10;

    /**
     * Some error occurred so this Index can not be trusted (at the moment)
     */
    const ERROR = -100;

    /**
     * Returns if the Index is capable to lookup the given value
     *
     * @param mixed $value Value to lookup
     * @return boolean
     */
    public function canLookup($value);

    /**
     * Looks up the given value and returns an array of the positions in the Database or one of the following constants:
     *
     * NOT_FOUND: The value was not found and thus does not exist in the managed collection - You don't have to query other Indexes
     * NO_RESULT: The Index can not provide a result for the lookup - You can query other Indexes
     * ERROR: A problem was detected - You can query other Indexes
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
