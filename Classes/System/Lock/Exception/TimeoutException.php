<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 17.10.14
 * Time: 13:47
 */
namespace Cundd\PersistentObjectStore\System\Lock;

use Cundd\PersistentObjectStore\RuntimeException;

/**
 * Exception thrown if a lock request runs into a timeout
 *
 * @package Cundd\PersistentObjectStore\System\Lock
 */
class TimeoutException extends RuntimeException {
}