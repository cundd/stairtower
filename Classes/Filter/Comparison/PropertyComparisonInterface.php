<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 01.09.14
 * Time: 21:04
 */

namespace Cundd\PersistentObjectStore\Filter\Comparison;

/**
 * Comparison interface
 *
 * @package Cundd\PersistentObjectStore\Filter
 */
interface PropertyComparisonInterface extends ComparisonInterface
{
    /**
     * Returns the property key of the test value that will be compared against the comparison value
     *
     * @return mixed
     */
    public function getProperty();

    /**
     * Returns the value to test against
     *
     * @return mixed
     */
    public function getValue();
}
