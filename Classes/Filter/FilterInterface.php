<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 01.09.14
 * Time: 20:52
 */

namespace Cundd\PersistentObjectStore\Filter;

use Cundd\PersistentObjectStore\Domain\Model\Database;
use Cundd\PersistentObjectStore\Filter\Comparison\ComparisonInterface;

/**
 * Interface for collection filters
 *
 * @package Cundd\PersistentObjectStore\Filter
 */
interface FilterInterface
{
    /**
     * Sets the comparison
     *
     * @param ComparisonInterface $comparison
     * @return $this
     */
    public function setComparison($comparison);

    /**
     * Returns the comparison
     *
     * @return ComparisonInterface
     */
    public function getComparison();

    /**
     * Returns the filter result
     *
     * @param Database|\Iterator|array $collection
     * @return FilterResultInterface
     */
    public function filterCollection($collection);

    /**
     * Returns if this collection item matches the comparisons
     *
     * @param mixed $item
     * @return bool
     */
    public function checkItem($item);
}