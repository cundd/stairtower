<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 03.03.15
 * Time: 20:57
 */

namespace Cundd\PersistentObjectStore\Aggregation;

use Cundd\PersistentObjectStore\Domain\Model\DatabaseInterface;
use Iterator;
use Traversable;

/**
 * Basic Aggregator implementation
 *
 * @package Cundd\PersistentObjectStore\Aggregation
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
     * Creates a new simple aggregator
     *
     * @param $aggregationCallback
     */
    public function __construct($aggregationCallback)
    {
        $this->aggregationCallback = $this->prepareCallback($aggregationCallback);
    }

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
        $this->results = array();
    }
}
