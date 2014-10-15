<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 14.10.14
 * Time: 20:15
 */

namespace Cundd\PersistentObjectStore\Filter;

use Cundd\PersistentObjectStore\Domain\Model\Database;
use Cundd\PersistentObjectStore\Filter\Comparison\PropertyComparison;
use Cundd\PersistentObjectStore\Filter\Comparison\PropertyComparisonInterface;

/**
 * FilterBuild implementation
 *
 * @package Cundd\PersistentObjectStore\Filter
 */
class FilterBuilder implements FilterBuilderInterface {
	/**
	 * Build a Filter with the given query parts
	 *
	 * @param array    $queryParts
	 * @param Database $collection
	 * @return FilterResult
	 */
	public function buildFilterFromQueryParts($queryParts, $collection) {
		$comparisons = array();
		foreach ($queryParts as $propertyKey => $testValue) {
			$comparisons[] = new PropertyComparison($propertyKey, PropertyComparisonInterface::TYPE_EQUAL_TO, $testValue);
//			$comparisons[] = new PropertyComparison($propertyKey, PropertyComparisonInterface::TYPE_LIKE, $testValue);
		}
		$filter = new Filter($comparisons);
		return $filter->filterCollection($collection);
	}

} 