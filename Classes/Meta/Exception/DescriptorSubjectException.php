<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 25.02.15
 * Time: 21:29
 */

namespace Cundd\PersistentObjectStore\Meta\Exception;

use Cundd\PersistentObjectStore\RuntimeException;
use Cundd\PersistentObjectStore\Utility\GeneralUtility;

/**
 * Exception thrown for invalid Descriptor subjects implementations
 *
 * @package Cundd\PersistentObjectStore\Meta\Exception
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
