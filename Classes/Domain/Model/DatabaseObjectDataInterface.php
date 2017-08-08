<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Domain\Model;

/**
 * Special database interface that describes the access to objects by index
 */
interface DatabaseObjectDataInterface
{
    /**
     * Returns the Document instance at the given index or sets it if it is not already set
     *
     * @param int $index
     * @return bool|DocumentInterface
     * @internal
     */
    public function getObjectDataForIndex($index);
} 