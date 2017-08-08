<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Filter;

use Cundd\Stairtower\Domain\Model\Database;
use Cundd\Stairtower\Filter\Comparison\ComparisonInterface;

/**
 * Interface for collection filters
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