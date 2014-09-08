<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 01.09.14
 * Time: 20:52
 */

namespace Cundd\PersistentObjectStore\Filter;
use Cundd\PersistentObjectStore\Domain\Model\Database;

/**
 * Interface for collection filters
 *
 * @package Cundd\PersistentObjectStore\Filter
 */
interface FilterInterface {
	/**
	 * Adds the given comparison
	 *
	 * Multiple comparisons will be added as "or"
	 *
	 * @param ComparisonInterface $comparison
	 * @return $this
	 */
	public function addComparison($comparison);

	/**
	 * Removes the given comparison
	 *
	 * @param ComparisonInterface $comparison
	 * @throws Exception\InvalidComparisonException if the given comparison is not in the list
	 * @return $this
	 */
	public function removeComparison($comparison);

	/**
	 * Sets the comparisons
	 *
	 * The comparisons will be added as "or"
	 *
	 * @param \SplObjectStorage(ComparisonInterface) $comparisons
	 * @return $this
	 */
	public function setComparisons(\SplObjectStorage $comparisons);

	/**
	 * Returns the comparisons
	 *
	 * @return \SplObjectStorage(ComparisonInterface)
	 */
	public function getComparisons();

	/**
	 * Returns the filter result
	 *
	 * @param Database|\Iterator $collection
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