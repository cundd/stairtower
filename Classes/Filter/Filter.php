<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 01.09.14
 * Time: 21:34
 */

namespace Cundd\PersistentObjectStore\Filter;

use Cundd\PersistentObjectStore\Domain\Model\Database;

use Cundd\PersistentObjectStore\Filter\Comparison\PropertyComparisonInterface;
use Cundd\PersistentObjectStore\Filter\Exception\InvalidCollectionException;
use Cundd\PersistentObjectStore\Filter\Exception\InvalidComparisonException;


/**
 * Filter implementation
 *
 * @package Cundd\PersistentObjectStore\Filter
 */
class Filter implements FilterInterface {
	/**
	 * Collection of comparisons
	 *
	 * @var \SplObjectStorage
	 */
	protected $comparisons;

	/**
	 * Creates a new filter
	 *
	 * @param \SplObjectStorage|array $comparisons The comparisons
	 */
	function __construct($comparisons = NULL) {
		if (!$comparisons) {
			$this->comparisons = new \SplObjectStorage();
		} else {
			$this->initWithComparisons($comparisons);
		}
	}

	/**
	 * Initialize the filter with the given comparisons
	 *
	 * @param $comparisons
	 * @return \SplObjectStorage
	 */
	public function initWithComparisons($comparisons) {
		$tempComparisons = new \SplObjectStorage();
		foreach ($comparisons as $comparison) {
			$tempComparisons->attach($comparison);
		}
		$tempComparisons->rewind();
		$this->comparisons = $tempComparisons;
	}


	/**
	 * Adds the given comparison
	 *
	 * Multiple comparisons will be added as "or"
	 *
	 * @param PropertyComparisonInterface $comparison
	 * @return $this
	 */
	public function addComparison($comparison) {
		$this->comparisons->attach($comparison);
	}

	/**
	 * Removes the given comparison
	 *
	 * @param PropertyComparisonInterface $comparison
	 * @throws Exception\InvalidComparisonException if the given comparison is not in the list
	 * @return $this
	 */
	public function removeComparison($comparison) {
		if (!$this->comparisons->contains($comparison)) throw new InvalidComparisonException('Can not remove given comparison because it is not in the list', 1409600320);
		$this->comparisons->detach($comparison);
	}

	/**
	 * Sets the comparisons
	 *
	 * The comparisons will be added as "or"
	 *
	 * @param \SplObjectStorage(ComparisonInterface) $comparisons
	 * @return $this
	 */
	public function setComparisons(\SplObjectStorage $comparisons) {
		$this->comparisons = $comparisons;
	}

	/**
	 * Returns the comparisons
	 *
	 * @return \SplObjectStorage(ComparisonInterface)
	 */
	public function getComparisons() {
		return $this->comparisons;
	}

	/**
	 * Returns the filter result
	 *
	 * @param Database|\Iterator|array $collection
	 * @throws Exception\InvalidCollectionException if the given collection is not valid
	 * @return FilterResultInterface
	 */
	public function filterCollection($collection) {
		if (!is_object($collection)) throw new InvalidCollectionException('No object given', 1410628879);
		if (!($collection instanceof \Iterator)) throw new InvalidCollectionException('Can not iterate over the given object', 1409603143);

//		return new FilterResult(new \IteratorIterator($collection), $this);
		return new FilterResult($collection, $this);
	}

	/**
	 * Returns if this collection item matches the comparisons
	 *
	 * @param mixed $item
	 * @return bool
	 */
	public function checkItem($item) {
		$comparisonCollection = $this->getComparisons();
		$comparisonCollection->rewind();

		while ($comparisonCollection->valid()) {
			/** @var PropertyComparisonInterface $comparison */
			$comparison = $comparisonCollection->current();
			if (!$comparison->perform($item)) {
				return FALSE;
			}
			$comparisonCollection->next();
		}
		return TRUE;
	}
}