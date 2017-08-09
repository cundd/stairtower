<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Domain\Model;

use Cundd\Stairtower\Constants;
use Cundd\Stairtower\Domain\Model\Exception\InvalidDataException;
use Cundd\Stairtower\LogicException;
use Cundd\Stairtower\Utility\GeneralUtility;
use Cundd\Stairtower\Utility\ObjectUtility;
use JsonSerializable;

/**
 * Class that represents a block of data
 */
class Document implements DocumentInterface, JsonSerializable
{
    protected $creationTime;
    protected $modificationTime;
    protected $databaseIdentifier;
    protected $id;
    protected $data;

    /**
     * Document constructor
     *
     * @param array  $data
     * @param string $databaseIdentifier
     */
    public function __construct($data = [], string $databaseIdentifier = '')
    {
        if ($data) {
            $this->assertDataType($data);
            $this->data = $data;
        }
        if ($databaseIdentifier) {
            GeneralUtility::assertDatabaseIdentifier($databaseIdentifier);
            $this->databaseIdentifier = $databaseIdentifier;
        }
    }

    public function getGuid(): string
    {
        return $this->getDatabaseIdentifier() . '-' . $this->getId();
    }

    public function getDatabaseIdentifier(): ?string
    {
        return $this->databaseIdentifier;
    }

    /**
     * Returns the associated database
     *
     * @param string $databaseIdentifier
     */
    public function setDatabaseIdentifier($databaseIdentifier)
    {
        GeneralUtility::assertDatabaseIdentifier($databaseIdentifier);
        $this->databaseIdentifier = $databaseIdentifier;
    }

    public function getId()
    {
        return $this->valueForKey(Constants::DATA_ID_KEY);
    }

    public function valueForKey(string $key)
    {
        if ($key === 'guid') {
            return $this->getGuid();
        }
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }

        return null;
    }

    public function getCreationTime(): ?int
    {
        return $this->creationTime;
    }

    /**
     * Returns the timestamp of the creation
     *
     * @param int $creationTime
     */
    public function setCreationTime(int $creationTime)
    {
        $this->creationTime = $creationTime;
    }

    /**
     * Returns the timestamp of the last modification
     *
     * @return int
     */
    public function getModificationTime(): ?int
    {
        return $this->modificationTime;
    }

    /**
     * Returns the timestamp of the last modification
     *
     * @param int $modificationTime
     */
    public function setModificationTime(int $modificationTime)
    {
        $this->modificationTime = $modificationTime;
    }

    public function setValueForKey($value, string $key)
    {
        if (!is_string($key)) {
            throw new LogicException(
                'Given key path is not of type string (maybe arguments are ordered incorrect)',
                1395484136
            );
        }
        $this->data[$key] = $value;
    }

    public function valueForKeyPath(string $keyPath)
    {
        if (!strpos($keyPath, '.')) {
            return $this->valueForKey($keyPath);
        }

        return ObjectUtility::valueForKeyPathOfObject($keyPath, $this->getData());
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    /**
     * Sets the underlying data
     *
     * @param array $data
     * @return Document
     */
    public function setData(array $data): Document
    {
        $this->assertDataType($data);
        $this->data = $data;

        return $this;
    }

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     *
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *       which is a value of any type other than a resource.
     */
    function jsonSerialize()
    {
        $objectData = $this->getData();
        $objectData[Constants::DATA_META_KEY] = [
            'guid'             => $this->getGuid(),
            'database'         => $this->getDatabaseIdentifier(),
            'creationTime'     => $this->getCreationTime(),
            'modificationTime' => $this->getModificationTime(),
        ];

        return $objectData;
    }

    /**
     * Assert if the data type is array
     *
     * @param array $data
     */
    protected function assertDataType($data)
    {
        if (!is_array($data)) {
            throw new InvalidDataException(
                sprintf('Given data is not of type array but %s', GeneralUtility::getType($data)),
                1423687533
            );
        }
    }
}