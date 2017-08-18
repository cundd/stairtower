<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Aggregation;

use Cundd\Stairtower\Domain\Model\DatabaseInterface;
use Iterator;

/**
 * Interface for aggregator implementations
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
