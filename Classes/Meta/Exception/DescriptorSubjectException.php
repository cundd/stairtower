<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Meta\Exception;

use Cundd\Stairtower\RuntimeException;
use Cundd\Stairtower\Utility\GeneralUtility;

/**
 * Exception thrown for invalid Descriptor subjects implementations
 */
class DescriptorSubjectException extends RuntimeException
{
    /**
     * Creates a new Descriptor exception with the given expected and actual classes
     *
     * @param string        $expected
     * @param string|object $actual
     * @param int           $code
     * @return DescriptorSubjectException
     */
    public static function descriptorException($expected, $actual, $code)
    {
        return new static(
            sprintf('Given subject is not of type %s but %s', $expected, GeneralUtility::getType($actual)),
            $code
        );
    }
}
