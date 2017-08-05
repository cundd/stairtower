<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Server\Exception;

use Cundd\PersistentObjectStore\LogicException;

/**
 * Exception thrown if a servers event loop is not configured
 */
class InvalidEventLoopException extends LogicException
{
} 