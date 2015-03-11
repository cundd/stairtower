<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 14.09.14
 * Time: 17:44
 */

namespace Cundd\PersistentObjectStore\Filter\Comparison;

/**
 * Interface for logical comparison
 *
 * @package Cundd\PersistentObjectStore\Filter\Comparison
 */
interface LogicalComparisonInterface extends ComparisonInterface
{
    /**
     * Returns the constraints
     *
     * @return array|\Iterator
     */
    public function getConstraints();
}
