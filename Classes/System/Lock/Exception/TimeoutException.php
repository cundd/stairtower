<?php
declare(strict_types=1);

namespace Cundd\Stairtower\System\Lock\Exception;

use Cundd\Stairtower\RuntimeException;

/**
 * Exception thrown if a lock request runs into a timeout
 */
class TimeoutException extends RuntimeException
{
}