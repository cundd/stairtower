<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Domain\Model;

use Countable;
use Cundd\Stairtower\ArrayableInterface;
use Cundd\Stairtower\Filter\Comparison\ComparisonInterface;
use Cundd\Stairtower\Filter\FilterResultInterface;
use Cundd\Stairtower\Index\IndexableInterface;
use Iterator;
use SeekableIterator;


/**
 * Interface for Database implementations
 */
interface DatabaseInterface extends DatabaseStateInterface, ArrayableInterface, IndexableInterface, Iterator, Countable, SeekableIterator
{
    /**
     * Returns the database identifier
     *
     * @return string
     */
    public function getIdentifier(): string;

    /**
     * Filters the database using the given comparison
     *
     * @param ComparisonInterface $comparison
     * @return FilterResultInterface
     */
    public function filter(ComparisonInterface $comparison): FilterResultInterface;

    /**
     * Returns the object with the given identifier
     *
     * @param string $identifier
     * @return DocumentInterface|null
     */
    public function findByIdentifier(string $identifier): ?DocumentInterface;

    /**
     * Sets the raw data
     *
     * [Optional]
     *
     * @param $rawData
     */
    // public function setRawData($rawData);


    // MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
    // MANAGING OBJECTS
    // MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW
    /**
     * Adds the given Document to the database
     *
     * @param DocumentInterface $document
     * @return DatabaseInterface
     */
    public function add(DocumentInterface $document): self;

    /**
     * Updates the given Document in the database
     *
     * @param DocumentInterface $document
     * @return DatabaseInterface
     */
    public function update(DocumentInterface $document): self;

    /**
     * Removes the given Document from the database
     *
     * @param DocumentInterface $document
     * @return DatabaseInterface
     */
    public function remove(DocumentInterface $document): self;

    /**
     * Returns if the database contains the given Document
     *
     * @param DocumentInterface|string $document Actual Document instance or it's GUID
     * @return bool
     */
    public function contains($document): bool;
}
