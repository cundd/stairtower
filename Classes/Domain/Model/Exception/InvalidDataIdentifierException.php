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
 * Exception thrown if a Document instances identifier is somehow invalid
 *
 * @package Cundd\PersistentObjectStore\Domain\Model\Exception
 */
class InvalidDataIdentifierException extends RuntimeException
{
}