<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 15.08.14
 * Time: 20:32
 */

namespace Cundd\PersistentObjectStore\Domain\Model\Exception;

use Cundd\PersistentObjectStore\RuntimeException;

/**
 * Exception thrown if an instance (e.g. a Database) is modified in an unsupported way
 *
 * @package Cundd\PersistentObjectStore\DataAccess\Exception
 */
class InvalidModificationException extends RuntimeException
{
}
