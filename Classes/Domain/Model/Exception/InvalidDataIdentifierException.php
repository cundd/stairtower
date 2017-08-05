<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Domain\Model\Exception;


use Cundd\PersistentObjectStore\RuntimeException;

/**
 * Exception thrown if a Document instances identifier is somehow invalid
 */
class InvalidDataIdentifierException extends RuntimeException
{
}