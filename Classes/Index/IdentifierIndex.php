<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 03.11.14
 * Time: 21:09
 */

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
 *
 * @package Cundd\PersistentObjectStore\Index
 */
class IdentifierIndex extends Key
{
    /**
     * Builds the index for the given collection
     *
     * @param DatabaseInterface|\Iterator $database
     * @return $this
     */
    public function indexDatabase($database)
    {
        // Clear the map
        $this->map = array();

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
        $count    = $collection->getSize();
        $tempMap  = array();

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
                    throw new DuplicateEntryException(sprintf('Duplicate entry \'%s\' for identifier', $key),
                        1415046937);
                }
                $tempMap[$key] = $position;
            } while (++$position < $count);
        }
        $this->map = $tempMap;
    }

    /**
     * Adds the given entry to the Index
     *
     * @param DocumentInterface|array $document
     * @param  int                    $position
     * @return $this
     */
    public function addEntryWithPosition($document, $position)
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
     * @return $this
     */
    public function updateEntryForPosition($document, $position)
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
     * @return $this
     */
    public function deleteEntry($document)
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
    public function getProperty()
    {
        return Constants::DATA_ID_KEY;
    }


}