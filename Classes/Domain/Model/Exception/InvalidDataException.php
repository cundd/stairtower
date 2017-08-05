<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Domain\Model\Exception;


use Cundd\PersistentObjectStore\RuntimeException;

/**
 * Exception thrown if a Document instance is somehow invalid
 */
class InvalidDataException extends RuntimeException
{
}