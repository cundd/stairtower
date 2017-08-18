<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Aggregation;

use Cundd\Stairtower\Domain\Model\DatabaseInterface;
use Iterator;
use Traversable;

/**
 * Basic Aggregator implementation
 */
class Aggregator extends AbstractAggregator implements AggregatorInterface
{
    /**
     * Container for the aggregation results
     *
     * @var Traversable|array
     */
    public $results;

    /**
     * Performs the aggregation operation on the given collection
     *
     * @param DatabaseInterface|Iterator|object[] $collection A collection of objects
     * @return array
     */
    public function perform($collection)
    {
        $this->prepareResultContainer();
        $this->performAggregation($collection);

        return $this->results;
    }

    /**
     * Prepares the container for the results
     */
    public function prepareResultContainer()
    {
        $this->results = [];
    }
}
