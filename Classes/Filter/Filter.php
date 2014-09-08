<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 01.09.14
 * Time: 21:34
 */

namespace Cundd\PersistentObjectStore\Filter;

use Cundd\PersistentObjectStore\Domain\Model\Database;
use Cundd\PersistentObjectStore\Domain\Model\DataInterface;
use Cundd\PersistentObjectStore\Filter\Exception\InvalidComparisonException;
use Cundd\PersistentObjectStore\Utility\ObjectUtility;

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
			$comparisons = new \SplObjectStorage();
		} else {
			$this->initWithComparisons($comparisons);
		}
		$this->comparisons = $comparisons;
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
	 * @param ComparisonInterface $comparison
	 * @return $this
	 */
	public function addComparison($comparison) {
		$this->comparisons->attach($comparison);
	}

	/**
	 * Removes the given comparison
	 *
	 * @param ComparisonInterface $comparison
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
	 * @param Database|\Iterator $collection
	 * @throws Exception\InvalidCollectionException if the given collection is not valid
	 * @return FilterResultInterface
	 */
	public function filterCollection($collection) {
		if (!($collection instanceof \Iterator)) throw new \Cundd\PersistentObjectStore\Filter\Exception\InvalidCollectionException('No object', 1409603143);
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
			/** @var ComparisonInterface $comparison */
			$comparison = $comparisonCollection->current();
			if (!$this->performComparison($item, $comparison)) {
				return FALSE;
			}
			$comparisonCollection->next();
		}
		return TRUE;
	}

	/**
	 * Executes the comparison on the item
	 *
	 * @param                     $item
	 * @param ComparisonInterface $comparison
	 * @return boolean
	 */
	protected function performComparison($item, $comparison) {
		if ($item instanceof DataInterface) {
			$item = $item->getData();
		}
		$propertyValue = ObjectUtility::valueForKeyPathOfObject($comparison->getProperty(), $item);
		switch ($comparison->getOperator()) {
			case ComparisonInterface::TYPE_EQUAL_TO:
				return $propertyValue === $comparison->getValue();

			case ComparisonInterface::TYPE_NOT_EQUAL_TO:
				return $propertyValue !== $comparison->getValue();

			case ComparisonInterface::TYPE_LESS_THAN:
				return $propertyValue < $comparison->getValue();

			case ComparisonInterface::TYPE_LESS_THAN_OR_EQUAL_TO:
				return $propertyValue <= $comparison->getValue();

			case ComparisonInterface::TYPE_GREATER_THAN:
				return $propertyValue > $comparison->getValue();

			case ComparisonInterface::TYPE_GREATER_THAN_OR_EQUAL_TO:
				return $propertyValue >= $comparison->getValue();

			case ComparisonInterface::TYPE_LIKE:
				return $propertyValue === $comparison->getValue();

			case ComparisonInterface::TYPE_CONTAINS:
				return $this->performsContains($propertyValue, $comparison->getValue());

			case ComparisonInterface::TYPE_IN:
				return $this->performsContains($comparison->getValue(), $propertyValue);

			case ComparisonInterface::TYPE_IS_NULL:
				return is_null($propertyValue);

			case ComparisonInterface::TYPE_IS_EMPTY:
				return !$propertyValue;
		}
		return FALSE;
	}

	/**
	 * @param array|\Traversable $collection
	 * @param mixed              $search
	 * @return bool
	 */
	protected function performsContains($collection, $search) {
		if ($collection instanceof \Traversable) {
			$collection = iterator_to_array($collection);
		}
		if (is_array($collection)) {
			return in_array($search, $collection);
		}
		if (is_string($collection)) {
			return strpos($collection, (string)$search) !== FALSE;
		}
		return FALSE;
	}

} 