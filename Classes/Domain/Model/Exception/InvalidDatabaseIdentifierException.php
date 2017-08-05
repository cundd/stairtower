<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Domain\Model\Exception;


use Cundd\PersistentObjectStore\RuntimeException;

/**
 * Exception thrown if an invalid data identifier is detected
 */
class InvalidDatabaseIdentifierException extends RuntimeException
{
}