<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 13.09.14
 * Time: 19:11
 */

namespace Cundd\PersistentObjectStore\Exception;

use Cundd\PersistentObjectStore\LogicException;

/**
 * Exception thrown if a required PHP extension is not available
 *
 * @package Cundd\PersistentObjectStore\Exception
 */
class MissingExtensionException extends LogicException
{
}