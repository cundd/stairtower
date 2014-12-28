<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 03.11.14
 * Time: 21:09
 */

namespace Cundd\PersistentObjectStore\Index;

use Cundd\PersistentObjectStore\Core\ArrayException\InvalidIndexException;
use Cundd\PersistentObjectStore\Domain\Model\DatabaseInterface;
use Cundd\PersistentObjectStore\Domain\Model\DatabaseRawDataInterface;
use Cundd\PersistentObjectStore\Domain\Model\DocumentInterface;
use Cundd\PersistentObjectStore\Index\Exception\DuplicateEntryException;
use Cundd\PersistentObjectStore\Index\Exception\InvalidEntryException;
use Cundd\PersistentObjectStore\Utility\DocumentUtility;
use Cundd\PersistentObjectStore\Utility\ObjectUtility;
use SplFixedArray;

/**
 * Key implementation
 *
 * @package Cundd\PersistentObjectStore\Index
 */
class Key extends AbstractIndex
{
    /**
     * Map of values to Database entry positions
     *
     * @var array
     */
    protected $map = array();

    /**
     * Returns if the Index is capable to lookup the given value
     *
     * @param mixed $value Value to lookup
     * @return boolean
     */
    public function canLookup($value)
    {
        return is_scalar($value);
    }

    /**
     * Looks up the given value and returns an array of the positions in the Database, NOT_FOUND if it was not found
     * or ERROR if a problem was detected
     *
     * @param mixed $value Value to look for
     * @return int[]
     */
    public function lookup($value)
    {
        if (isset($this->map[$value])) {
            return array($this->map[$value]);
        }
        return self::NOT_FOUND;
    }

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
        }

        if ($database instanceof SplFixedArray) {
            // Use the fixed array as is
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
        if ($count > 0) {
            do {
                $tempEntry = DocumentUtility::assertDocumentIdentifier($database[$position]);
                $this->addEntryWithPosition($tempEntry, $position);
            } while (++$position < $count);
        }
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
        $key = ObjectUtility::valueForKeyPathOfObject($this->getProperty(), $document);
        if (isset($this->map[$key])) {
            throw new DuplicateEntryException(sprintf('Duplicate entry \'%s\' for key %s', $key, $this->getProperty()),
                1415046937);
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
        $key = ObjectUtility::valueForKeyPathOfObject($this->getProperty(), $document);
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
        $key = ObjectUtility::valueForKeyPathOfObject($this->getProperty(), $document);
        if (!isset($this->map[$key])) {
            throw new InvalidEntryException(sprintf('Entry \'%s\' not found to delete', $key), 1415047176);
        }
        unset($this->map[$key]);
        return $this;
    }

    /**
     * Returns if the position already exists in the Index
     *
     * @param int $position
     * @return bool
     */
    protected function _positionIsDefined($position)
    {
        return in_array($position, $this->map);
    }
}