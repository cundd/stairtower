<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Exception;

use Cundd\Stairtower\LogicException;

/**
 * Exception thrown if a required PHP extension is not available
 */
class MissingExtensionException extends LogicException
{
}