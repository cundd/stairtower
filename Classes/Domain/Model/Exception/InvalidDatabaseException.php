<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Domain\Model\Exception;


use Cundd\PersistentObjectStore\RuntimeException;

/**
 * Exception thrown if a Database is invalid
 *
 * This may occur if a Database to create already exists or a Database to delete does not exist
 */
class InvalidDatabaseException extends RuntimeException
{
} 