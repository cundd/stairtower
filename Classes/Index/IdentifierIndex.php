<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Index;

use Cundd\PersistentObjectStore\Constants;
use Cundd\PersistentObjectStore\Core\ArrayException\InvalidIndexException;
use Cundd\PersistentObjectStore\Domain\Model\DatabaseInterface;
use Cundd\PersistentObjectStore\Domain\Model\DatabaseRawDataInterface;
use Cundd\PersistentObjectStore\Domain\Model\DocumentInterface;
use Cundd\PersistentObjectStore\Index\Exception\DuplicateEntryException;
use Cundd\PersistentObjectStore\Index\Exception\InvalidEntryException;
use Cundd\PersistentObjectStore\Utility\DebugUtility;
use Cundd\PersistentObjectStore\Utility\DocumentUtility;
use SplFixedArray;

/**
 * Key implementation
 */
class IdentifierIndex extends Key
{
    /**
     * Builds the index for the given collection
     *
     * @param DatabaseInterface|\Iterator|\Traversable $database
     * @return IndexInterface
     */
    public function indexDatabase(\Traversable $database): IndexInterface
    {
        // Clear the map
        $this->map = [];

        /** @var SplFixedArray $collection */
        $collection = null;

        if ($database instanceof DatabaseRawDataInterface) {
            $collection = $database->getRawData();
        } elseif ($database instanceof SplFixedArray) {
            $collection = $database;
        } elseif (is_array($database)) {
            $collection = SplFixedArray::fromArray($database);
        } elseif ($database instanceof \Iterator) {
            $collection = SplFixedArray::fromArray(iterator_to_array($database));
        } else {
            throw new InvalidIndexException(
                sprintf(
                    'Can not build index of argument of type %s',
                    is_object($database) ? get_class($database) : gettype($database)
                )
            );
        }

        $position = 0;
        $count = $collection->getSize();
        $tempMap = [];

        if ($count > 0) {
            $collectionContainsDocumentObjects = $collection[0] instanceof DocumentInterface;

            do {
                $entry = $collection[$position];
                if ($collectionContainsDocumentObjects) {
                    $key = DocumentUtility::assertDocumentIdentifier($entry)->getId();
                } else {
                    $key = DocumentUtility::getIdentifierForDocument($entry);
                }

//				DebugUtility::var_dump('Index', $key);

                if (isset($tempMap[$key])) {
                    throw new DuplicateEntryException(
                        sprintf('Duplicate entry \'%s\' for identifier', $key),
                        1415046937
                    );
                }
                $tempMap[$key] = $position;
            } while (++$position < $count);
        }
        $this->map = $tempMap;

        return $this;
    }

    /**
     * Adds the given entry to the Index
     *
     * @param DocumentInterface|array $document
     * @param  int                    $position
     * @return IndexInterface
     */
    public function addEntryWithPosition($document, int $position): IndexInterface
    {
        $key = DocumentUtility::getIdentifierForDocument($document);
        if (isset($this->map[$key])) {
            throw new DuplicateEntryException(sprintf('Duplicate entry \'%s\' for identifier', $key), 1415046937);
        }
        $this->map[$key] = $position;

        return $this;
    }

    /**
     * Updates the given entry in the Index
     *
     * @param DocumentInterface|array $document
     * @param int                     $position
     * @return IndexInterface
     */
    public function updateEntryForPosition($document, int $position): IndexInterface
    {
        $key = DocumentUtility::getIdentifierForDocument($document);
        if (!isset($this->map[$key])) {
            throw new InvalidEntryException(sprintf('Entry \'%s\' not found to update', $key), 1415047116);
        }
        $this->map[$key] = $position;

        return $this;
    }

    /**
     * Removes the given entry in the Index
     *
     * @param DocumentInterface|array $document
     * @return IndexInterface
     */
    public function deleteEntry($document): IndexInterface
    {
        DebugUtility::var_dump($this->map);
        $key = DocumentUtility::getIdentifierForDocument($document);
        if (!isset($this->map[$key])) {
            throw new InvalidEntryException(sprintf('Entry \'%s\' not found to delete', $key), 1415047176);
        }
        unset($this->map[$key]);

        DebugUtility::var_dump($this->map);

        return $this;
    }

    /**
     * Returns the property key to be indexed
     *
     * @return string
     */
    public function getProperty(): string
    {
        return Constants::DATA_ID_KEY;
    }
}
