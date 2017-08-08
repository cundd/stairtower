<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Exception;


use Cundd\Stairtower\RuntimeException;

/**
 * Exception thrown if an immutable object is tried to modify
 */
class ImmutableException extends RuntimeException
{
}