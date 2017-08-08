<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server\Exception;

/**
 * Exception thrown if a POST requests Content-Length header is missing
 */
class MissingLengthHeaderException extends InvalidRequestException
{
} 