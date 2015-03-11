<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 01.09.14
 * Time: 22:25
 */

namespace Cundd\PersistentObjectStore\Filter\Exception;

use Cundd\PersistentObjectStore\RuntimeException;

/**
 * Exception thrown if the input passed to the Filter Builder can not be transformed into a Filter
 *
 * @package Cundd\PersistentObjectStore\Filter\Exception
 */
class InvalidFilterBuilderInputException extends RuntimeException
{
}
