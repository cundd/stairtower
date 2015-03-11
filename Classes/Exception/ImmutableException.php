<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 13.09.14
 * Time: 19:11
 */

namespace Cundd\PersistentObjectStore\Exception;

use Cundd\PersistentObjectStore\RuntimeException;

/**
 * Exception thrown if an immutable object is tried to modify
 *
 * @package Cundd\PersistentObjectStore\Exception
 */
class ImmutableException extends RuntimeException
{
}
