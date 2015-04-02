<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 11.10.14
 * Time: 14:28
 */

namespace Cundd\PersistentObjectStore\Server\ValueObject;


use Cundd\PersistentObjectStore\Immutable;
use Cundd\PersistentObjectStore\Server\Handler\HandlerResultInterface;

/**
 * Class to signal deferred results implementation
 *
 * @package Cundd\PersistentObjectStore\Server\ValueObject
 */
class DeferredResult implements HandlerResultInterface, Immutable
{
    /**
     * Returns the request's response data
     *
     * @return mixed
     */
    public function getData()
    {
        return null;
    }

    /**
     * Returns the status code for the response
     *
     * @return integer
     */
    public function getStatusCode()
    {
        return 204;
    }

    /**
     * Returns the singleton instance
     *
     * @return DeferredResult
     */
    public static function instance()
    {
        static $instance;
        if (!$instance) {
            $instance = new static();
        }

        return $instance;
    }
}
