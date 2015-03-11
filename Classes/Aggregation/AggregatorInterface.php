<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 03.03.15
 * Time: 20:53
 */

namespace Cundd\PersistentObjectStore\Aggregation;

use Cundd\PersistentObjectStore\Domain\Model\DatabaseInterface;
use Iterator;

/**
 * Interface for aggregator implementations
 *
 * @package Cundd\PersistentObjectStore\Aggregation
 */
interface AggregatorInterface
{
    /**
     * Performs the aggregation operation on the given collection
     *
     * @param DatabaseInterface|Iterator|object[] $collection A collection of objects
     * @return array
     */
    public function perform($collection);

    /**
     * Returns if the aggregation function has to be invoked for the given item
     *
     * @param object $item
     * @return bool
     */
    public function needToPerformAggregationForItem($item);
}
