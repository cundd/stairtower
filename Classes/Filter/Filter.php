<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 01.09.14
 * Time: 21:34
 */

namespace Cundd\PersistentObjectStore\Filter;

use Cundd\PersistentObjectStore\Domain\Model\Database;
use Cundd\PersistentObjectStore\Filter\Comparison\ComparisonInterface;
use Cundd\PersistentObjectStore\Filter\Exception\InvalidCollectionException;
use Cundd\PersistentObjectStore\Filter\Exception\InvalidComparisonException;


/**
 * Filter implementation
 *
 * @package Cundd\PersistentObjectStore\Filter
 */
class Filter implements FilterInterface
{
    /**
     * Comparison to use for this filter
     *
     * This may be a nested Logical Comparison
     *
     * @var ComparisonInterface
     */
    protected $comparison;

    /**
     * Creates a new filter
     *
     * @param \SplObjectStorage|ComparisonInterface[]|ComparisonInterface $comparison The comparison to filter by as a single Comparison instance
     */
    public function __construct($comparison = null)
    {
        if ($comparison) {
            $this->comparison = $comparison;
        }
    }

    /**
     * Returns the filter result
     *
     * @param Database|\Iterator|array $collection
     * @throws Exception\InvalidCollectionException if the given collection is not valid
     * @return FilterResultInterface
     */
    public function filterCollection($collection)
    {
        if (!is_object($collection)) {
            throw new InvalidCollectionException('No object given', 1410628879);
        }
        if (!($collection instanceof \Iterator)) {
            throw new InvalidCollectionException('Can not iterate over the given object', 1409603143);
        }

//		return new FilterResult(new \IteratorIterator($collection), $this);
        return new FilterResult($collection, $this);
    }

    /**
     * Returns if this collection item matches the comparison
     *
     * @param mixed $item
     * @return bool
     */
    public function checkItem($item)
    {
        $comparison = $this->getComparison();
        if (!$comparison) {
            throw new InvalidComparisonException('No comparison defined', 1420037779);
        }
        if (!$comparison instanceof ComparisonInterface) {
            throw new InvalidComparisonException(
                sprintf('Given comparison is of type %s',
                    is_object($comparison) ? get_class($comparison) : gettype($comparison)),
                1420038127
            );
        }
        return $this->comparison->perform($item);
    }

    /**
     * Returns the comparison
     *
     * @return ComparisonInterface
     */
    public function getComparison()
    {
        return $this->comparison;
    }

    /**
     * Sets the comparison
     *
     * @param ComparisonInterface $comparison
     * @return $this
     */
    public function setComparison($comparison)
    {
        $this->comparison = $comparison;
    }
}