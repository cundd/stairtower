<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Filter\Comparison;

/**
 * Interface for logical comparison
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