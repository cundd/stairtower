<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 11.10.14
 * Time: 15:19
 */

namespace Cundd\PersistentObjectStore\Server\Exception;

/**
 * Exception thrown if a POST requests Content-Length header is missing
 *
 * @package Cundd\PersistentObjectStore\Server\Exception
 */
class MissingLengthHeaderException extends InvalidRequestException
{
} 