<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Aggregation;

/**
 * Interface for MapReduce implementation
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
