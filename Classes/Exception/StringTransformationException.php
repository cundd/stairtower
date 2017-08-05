<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 13.09.14
 * Time: 19:11
 */

namespace Cundd\PersistentObjectStore\Exception;

use Cundd\PersistentObjectStore\ErrorException;

/**
 * Exception thrown if an object could not be converted to a string
 *
 * @package Cundd\PersistentObjectStore\Exception
 */
class StringTransformationException extends ErrorException
{
}