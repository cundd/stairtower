<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Domain\Model\Exception;


use Cundd\Stairtower\RuntimeException;

/**
 * Exception thrown if a Document instance is somehow invalid
 */
class InvalidDataException extends RuntimeException
{
}