<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Domain\Model;

/**
 * Special database interface that describes the access to objects by index
 */
interface DatabaseObjectDataInterface
{
    /**
     * Returns the Document instance at the given index or stores it if not already set
     *
     * @param int $index
     * @return DocumentInterface|bool
     * @internal
     */
    public function getObjectDataForIndex($index);
} 