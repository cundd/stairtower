<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 25.02.15
 * Time: 21:08
 */

namespace Cundd\PersistentObjectStore\Meta\Database\Property;


use Cundd\PersistentObjectStore\Immutable;
use JsonSerializable;

/**
 * Description for a property
 *
 * @package Cundd\PersistentObjectStore\Meta\Database
 */
class Description implements Immutable, JsonSerializable
{
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_INTEGER = 'integer';
    const TYPE_DOUBLE = 'double';
    const TYPE_STRING = 'string';
    const TYPE_ARRAY = 'array';
    const TYPE_OBJECT = 'object';
    const TYPE_RESOURCE = 'resource';
    const TYPE_NULL = 'NULL';
    const TYPE_UNKNOWN_TYPE = 'unknown_type';

    /**
     * Name
     *
     * @var string
     */
    protected $key = '';

    /**
     * Used types
     *
     * @var array
     */
    protected $types = array();

    /**
     * Number of occurrence
     *
     * @var int
     */
    protected $count = 0;

    function __construct($key, $types, $count)
    {
        $this->key   = $key;
        $this->types = $types;
        $this->count = $count;
    }

    /**
     * Returns the name
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Returns the used types
     *
     * @return array
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * Returns the number of occurrence
     *
     * @return int
     */
    public function getCount()
    {
        return $this->count;
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
        return array(
            'key'   => $this->getKey(),
            'count' => $this->getCount(),
            'types' => $this->getTypes(),
        );
    }


}