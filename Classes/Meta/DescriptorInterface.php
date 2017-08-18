<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Meta;

/**
 * Interface for Descriptors
 */
interface DescriptorInterface
{
    /**
     * Returns the description of the subject
     *
     * @param mixed $subject
     * @return mixed
     */
    public function describe($subject);
}
