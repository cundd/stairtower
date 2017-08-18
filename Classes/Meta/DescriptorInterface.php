<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Meta;

use Cundd\Stairtower\Domain\Model\DatabaseRawDataInterface;
use Cundd\Stairtower\Meta\Database\Property\Description;

/**
 * Interface for Descriptors
 */
interface DescriptorInterface
{
    /**
     * Returns a dictionary of Description objects for the given subject
     *
     * @param DatabaseRawDataInterface $subject
     * @return Description[]
     */
    public function describe($subject): array;
}
