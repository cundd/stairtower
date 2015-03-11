<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 15.08.14
 * Time: 20:12
 */

namespace Cundd\PersistentObjectStore\Domain\Model;

use Cundd\PersistentObjectStore\Constants;
use Cundd\PersistentObjectStore\Domain\Model\Exception\InvalidDataException;
use Cundd\PersistentObjectStore\LogicException;
use Cundd\PersistentObjectStore\Utility\GeneralUtility;
use Cundd\PersistentObjectStore\Utility\ObjectUtility;
use \JsonSerializable;

/**
 * Class that represents a block of data
 *
 * @package Cundd\PersistentObjectStore
 */
class Document implements DocumentInterface, JsonSerializable
{
    protected $creationTime;
    protected $modificationTime;
    protected $databaseIdentifier;
    protected $id;
    protected $data;

    public function __construct($data = array(), $databaseIdentifier = '')
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

    /**
     * Returns the global unique identifier
     *
     * @return string
     */
    public function getGuid()
    {
        return $this->getDatabaseIdentifier() . '-' . $this->getId();
    }


    /**
     * Returns the associated database
     *
     * @return string
     */
    public function getDatabaseIdentifier()
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

    /**
     * Returns the ID
     *
     * @return string
     */
    public function getId()
    {
        return $this->valueForKey(Constants::DATA_ID_KEY);
    }

    /**
     * Returns the value for the given key from the data
     *
     * @param string $key
     * @return mixed
     */
    public function valueForKey($key)
    {
        if ($key === 'guid') {
            return $this->getGuid();
        }
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
        return null;
    }

    /**
     * Returns the timestamp of the creation
     *
     * @return int
     */
    public function getCreationTime()
    {
        return $this->creationTime;
    }

    /**
     * Returns the timestamp of the creation
     *
     * @param int $creationTime
     */
    public function setCreationTime($creationTime)
    {
        $this->creationTime = $creationTime;
    }

    /**
     * Returns the timestamp of the last modification
     *
     * @return int
     */
    public function getModificationTime()
    {
        return $this->modificationTime;
    }

    /**
     * Returns the timestamp of the last modification
     *
     * @param int $modificationTime
     */
    public function setModificationTime($modificationTime)
    {
        $this->modificationTime = $modificationTime;
    }

    /**
     * Sets the value for the given key from the data
     *
     * @param mixed  $value
     * @param string $key
     * @throws LogicException
     */
    public function setValueForKey($value, $key)
    {
        if (!is_string($key)) {
            throw new LogicException('Given key path is not of type string (maybe arguments are ordered incorrect)',
                1395484136);
        }
        $this->data[$key] = $value;
    }

    /**
     * Returns the value for the given key path from the data
     *
     * @param string $keyPath
     * @return mixed
     */
    public function valueForKeyPath($keyPath)
    {
        if (!strpos($keyPath, '.')) {
            return $this->valueForKey($keyPath);
        }
        return ObjectUtility::valueForKeyPathOfObject($keyPath, $this->getData());
    }

    /**
     * Returns the underlying data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Returns the underlying data
     *
     * @param array $data
     */
    public function setData($data)
    {
        $this->assertDataType($data);
        $this->data = $data;
    }

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     *
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *       which is a value of any type other than a resource.
     */
    public function jsonSerialize()
    {
        $objectData                           = $this->getData();
        $objectData[Constants::DATA_META_KEY] = array(
            'guid'             => $this->getGuid(),
            'database'         => $this->getDatabaseIdentifier(),
            'creationTime'     => $this->getCreationTime(),
            'modificationTime' => $this->getModificationTime(),
        );
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
