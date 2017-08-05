<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Domain\Model\Exception;


use Cundd\PersistentObjectStore\RuntimeException;

/**
 * Exception thrown if an instance (e.g. a Database) is modified in an unsupported way
 */
class InvalidModificationException extends RuntimeException
{
} 