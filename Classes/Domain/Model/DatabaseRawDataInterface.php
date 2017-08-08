<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Domain\Model;

use Cundd\Stairtower\Core\ArrayException\IndexOutOfRangeException;
use Cundd\Stairtower\Filter\Exception\InvalidCollectionException;

/**
 * Special database interface that describes the access to raw data
 */
interface DatabaseRawDataInterface
{
    /**
     * Sets the raw data
     *
     * @param \SplFixedArray|array|\Iterator $rawData
     * @throws InvalidCollectionException if the given data can not be used
     * @internal
     */
    public function setRawData($rawData);

    /**
     * Returns the raw data
     *
     * @return \SplFixedArray
     * @internal
     */
    public function getRawData();

    /**
     * Returns the current raw data
     *
     * @return mixed Can return any type
     * @throws IndexOutOfRangeException if the current index is out of range
     */
    public function currentRaw();
} 