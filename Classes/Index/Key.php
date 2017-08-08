<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Index;

use Cundd\Stairtower\Core\ArrayException\InvalidIndexException;
use Cundd\Stairtower\Domain\Model\DatabaseInterface;
use Cundd\Stairtower\Domain\Model\DatabaseRawDataInterface;
use Cundd\Stairtower\Domain\Model\DocumentInterface;
use Cundd\Stairtower\Index\Exception\DuplicateEntryException;
use Cundd\Stairtower\Index\Exception\InvalidEntryException;
use Cundd\Stairtower\Utility\DocumentUtility;
use Cundd\Stairtower\Utility\ObjectUtility;
use SplFixedArray;

/**
 * Key implementation
 */
class Key extends AbstractIndex
{
    /**
     * Map of values to Database entry positions
     *
     * @var array
     */
    protected $map = [];

    /**
     * Returns if the Index is capable to lookup the given value
     *
     * @param mixed $value Value to lookup
     * @return bool
     */
    public function canLookup($value): bool
    {
        return is_scalar($value);
    }

    public function lookup($value)
    {
        if (isset($this->map[$value])) {
            return [$this->map[$value]];
        }

        return self::NOT_FOUND;
    }


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
        $count = $collection->getSize();
        if ($count > 0) {
            do {
                $tempEntry = DocumentUtility::assertDocumentIdentifier($database[$position]);
                $this->addEntryWithPosition($tempEntry, $position);
            } while (++$position < $count);
        }

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
        $key = ObjectUtility::valueForKeyPathOfObject($this->getProperty(), $document);
        if (isset($this->map[$key])) {
            throw new DuplicateEntryException(
                sprintf('Duplicate entry \'%s\' for key %s', $key, $this->getProperty()),
                1415046937
            );
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
     * @return IndexInterface
     */
    public function deleteEntry($document): IndexInterface
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
    protected function positionIsDefined($position)
    {
        return in_array($position, $this->map);
    }
}