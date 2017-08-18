<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Aggregation\Exception;

use Cundd\Stairtower\RuntimeException;

/**
 * Exception thrown for an invalid key when calling emit
 */
class InvalidEmitKeyException extends RuntimeException
{
}
