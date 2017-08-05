<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Exception;

use Cundd\PersistentObjectStore\ErrorException;

/**
 * Exception thrown if an object could not be converted to a string
 */
class StringTransformationException extends ErrorException
{
}