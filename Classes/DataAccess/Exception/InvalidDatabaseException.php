<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 15.08.14
 * Time: 20:32
 */

namespace Cundd\PersistentObjectStore\DataAccess\Exception;


use Cundd\PersistentObjectStore\RuntimeException;

/**
 * Exception thrown if a Database is invalid
 *
 * This may occur if a Database to create already exists or a Database to delete does not exist
 *
 * @package Cundd\PersistentObjectStore\DataAccess\Exception
 */
class InvalidDatabaseException extends RuntimeException {
} 