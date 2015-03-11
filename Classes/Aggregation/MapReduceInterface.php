<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 28.02.15
 * Time: 12:14
 */

namespace Cundd\PersistentObjectStore\Aggregation;

/**
 * Interface for MapReduce implementation
 *
 * @package Cundd\PersistentObjectStore\MapReduce
 */
interface MapReduceInterface extends AggregatorInterface
{
    /**
     * Adds the value for the given key to the results
     *
     * @param string $key
     * @param mixed  $value
     */
    public function emit($key, $value);
}
