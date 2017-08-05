<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Exception;

use Cundd\PersistentObjectStore\LogicException;

/**
 * Exception thrown if a required PHP extension is not available
 */
class MissingExtensionException extends LogicException
{
}