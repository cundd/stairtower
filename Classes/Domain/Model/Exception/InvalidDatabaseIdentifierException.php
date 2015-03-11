<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 25.08.14
 * Time: 21:42
 */

namespace Cundd\PersistentObjectStore\Domain\Model\Exception;

use Cundd\PersistentObjectStore\RuntimeException;

/**
 * Exception thrown if an invalid data identifier is detected
 *
 * @package Cundd\PersistentObjectStore\Exception
 */
class InvalidDatabaseIdentifierException extends RuntimeException
{
}
