<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\System\Lock\Exception;

use Cundd\PersistentObjectStore\RuntimeException;

/**
 * Exception thrown if a lock request runs into a timeout
 */
class TimeoutException extends RuntimeException
{
}