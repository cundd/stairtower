<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Server\Exception;

/**
 * Exception thrown if a servers IP address or port are tried to be changed while the server is running
 */
class InvalidServerChangeException extends ServerException
{
} 