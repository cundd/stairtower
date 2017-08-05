<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Filter\Exception;


use Cundd\PersistentObjectStore\RuntimeException;

/**
 * Exception thrown if the input passed to the Filter Builder can not be transformed into a Filter
 */
class InvalidFilterBuilderInputException extends RuntimeException
{
} 