<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Filter\Comparison;

/**
 * Comparison interface
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