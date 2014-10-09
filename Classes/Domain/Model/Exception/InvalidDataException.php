<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 09.10.14
 * Time: 14:55
 */

namespace Cundd\PersistentObjectStore\Domain\Model\Exception;


use Cundd\PersistentObjectStore\RuntimeException;

/**
 * Exception thrown if a Data instance is somehow invalid
 *
 * @package Cundd\PersistentObjectStore\Domain\Model\Exception
 */
class InvalidDataException extends RuntimeException {
}