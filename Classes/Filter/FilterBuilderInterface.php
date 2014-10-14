<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 14.10.14
 * Time: 20:11
 */

namespace Cundd\PersistentObjectStore\Filter;
use Cundd\PersistentObjectStore\Domain\Model\Database;

/**
 * Interface for classes that transform different types of input data to comparisons
 *
 * @package Cundd\PersistentObjectStore\Filter
 */
interface FilterBuilderInterface {
	/**
	 * Build a Filter with the given query parts
	 *
	 * @param array $queryParts
	 * @param Database|\Iterator$collection
	 * @return FilterResult
	 */
	public function buildFilterFromQueryParts($queryParts, $collection);
} 