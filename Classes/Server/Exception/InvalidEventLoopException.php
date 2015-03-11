<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 10.10.14
 * Time: 14:05
 */
namespace Cundd\PersistentObjectStore\Server\Exception;

use Cundd\PersistentObjectStore\LogicException;

/**
 * Exception thrown if a servers event loop is not configured
 *
 * @package Cundd\PersistentObjectStore\Server\Exception
 */
class InvalidEventLoopException extends LogicException
{
}
