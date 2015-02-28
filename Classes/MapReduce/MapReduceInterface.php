<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 28.02.15
 * Time: 12:14
 */

namespace Cundd\PersistentObjectStore\MapReduce;


use Cundd\PersistentObjectStore\Domain\Model\DatabaseInterface;

/**
 * Interface for MapReduce implementation
 *
 * @package Cundd\PersistentObjectStore\MapReduce
 */
interface MapReduceInterface
{
    /**
     * Performs the MapReduce operations on the given collection
     *
     * @param DatabaseInterface|\Iterator|array $collection
     * @return array
     */
    public function perform($collection);

    /**
     * Adds the value for the given key to the results
     *
     * @param string $key
     * @param mixed  $value
     */
    public function emit($key, $value);

    /**
     * Returns if the mapping function has to be invoked for the given item identifier
     *
     * @param string $identifier
     * @return bool
     */
    public function needToInvokeMapForIdentifier($identifier);
}