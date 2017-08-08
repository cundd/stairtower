<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Domain\Model\Exception;


use Cundd\Stairtower\RuntimeException;

/**
 * Exception thrown if a Document instances identifier is somehow invalid
 */
class InvalidDataIdentifierException extends RuntimeException
{
}