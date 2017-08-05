<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Expand\Exception;


use Cundd\PersistentObjectStore\RuntimeException;

/**
 * Exception thrown if the input passed to the Expand Builder can not be transformed
 */
class InvalidExpandBuilderInputException extends RuntimeException
{
} 