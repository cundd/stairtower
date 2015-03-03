<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 28.02.15
 * Time: 13:30
 */

namespace Cundd\PersistentObjectStore\Aggregation\Exception;


use Cundd\PersistentObjectStore\RuntimeException;

/**
 * Exception thrown for an invalid key when calling emit
 *
 * @package Cundd\PersistentObjectStore\MapReduce\Exception
 */
class InvalidEmitKeyException extends RuntimeException
{
}