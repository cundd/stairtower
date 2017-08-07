<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Index;

use Cundd\PersistentObjectStore\Domain\Model\DatabaseInterface;
use Cundd\PersistentObjectStore\Domain\Model\DocumentInterface;

/**
 * Interface for Index implementation
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
     * @return bool
     */
    public function canLookup($value): bool;

    /**
     * Looks up the given value and returns an array of the positions in the Database or one of the following constants:
     *
     * NOT_FOUND: The value was not found and thus does not exist in the managed collection - You don't have to query other Indexes
     * NO_RESULT: The Index can not provide a result for the lookup - You can query other Indexes
     * ERROR: A problem was detected - You can query other Indexes
     *
     * @param mixed $value Value to look for
     * @return int[]|int
     */
    public function lookup($value);

    /**
     * Adds the given entry to the Index
     *
     * @param DocumentInterface|array $document
     * @param int                     $position
     * @return IndexInterface
     */
    public function addEntryWithPosition($document, int $position): IndexInterface;

    /**
     * Updates the given entry in the Index
     *
     * @param DocumentInterface|array $document
     * @param int                     $position
     * @return IndexInterface
     */
    public function updateEntryForPosition($document, int $position): IndexInterface;

    /**
     * Removes the given entry in the Index
     *
     * @param DocumentInterface|array $document
     * @return IndexInterface
     */
    public function deleteEntry($document): IndexInterface;

    /**
     * Builds the index for the given collection
     *
     * @param DatabaseInterface|\Traversable $database
     * @return IndexInterface
     */
    public function indexDatabase(\Traversable $database): IndexInterface;

    /**
     * Returns the property key to be indexed
     *
     * @return string
     */
    public function getProperty(): string;
}
