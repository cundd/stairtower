<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Domain\Model\Exception;


use Cundd\Stairtower\RuntimeException;

/**
 * Exception thrown if an invalid data identifier is detected
 */
class InvalidDatabaseIdentifierException extends RuntimeException
{
}