<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Domain\Model\Exception;


use Cundd\Stairtower\RuntimeException;

/**
 * Exception thrown if an instance (e.g. a Database) is modified in an unsupported way
 */
class InvalidModificationException extends RuntimeException
{
} 