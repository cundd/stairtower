<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 10.10.14
 * Time: 17:55
 */

namespace Cundd\PersistentObjectStore\Server\Exception;

/**
 * Exception thrown if a servers IP address or port are tried to be changed while the server is running
 *
 * @package Cundd\PersistentObjectStore\Server\Exception
 */
class InvalidServerChangeException extends ServerException
{
} 