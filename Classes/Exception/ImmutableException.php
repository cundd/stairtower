<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Exception;


use Cundd\PersistentObjectStore\RuntimeException;

/**
 * Exception thrown if an immutable object is tried to modify
 */
class ImmutableException extends RuntimeException
{
}