<?php
declare(strict_types=1);

namespace Cundd\Stairtower;

use SplFixedArray;

/**
 * Interface that allows transformation to arrays and fixed array
 */
interface ArrayableInterface
{
    /**
     * Returns the filtered items as array
     *
     * @return array
     */
    public function toArray(): array;

    /**
     * Returns the filtered items as fixed array
     *
     * @return SplFixedArray
     */
    public function toFixedArray(): SplFixedArray;
}
