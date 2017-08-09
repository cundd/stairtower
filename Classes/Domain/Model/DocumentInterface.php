<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Domain\Model;

use Cundd\Stairtower\KeyValueCodingInterface;

/**
 * Abstract interface to describe the parameters of a persisted object
 */
interface DocumentInterface extends KeyValueCodingInterface
{
    /**
     * Returns the timestamp of the creation
     *
     * @return int
     */
    public function getCreationTime(): ?int;

    /**
     * Returns the timestamp of the last modification
     *
     * @return int
     */
    public function getModificationTime(): ?int;

    /**
     * Returns the associated database
     *
     * @return string
     */
    public function getDatabaseIdentifier(): ?string;

    /**
     * Returns the global unique identifier
     *
     * @return string
     */
    public function getGuid(): string;

    /**
     * Returns the ID
     *
     * @return string|int|null
     */
    public function getId();

    /**
     * Returns the underlying data
     *
     * @return array
     */
    public function getData(): ?array;
}