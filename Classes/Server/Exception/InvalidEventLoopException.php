<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server\Exception;

use Cundd\Stairtower\LogicException;

/**
 * Exception thrown if a servers event loop is not configured
 */
class InvalidEventLoopException extends LogicException
{
} 