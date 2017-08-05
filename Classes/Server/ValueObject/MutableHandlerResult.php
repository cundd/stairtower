<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 11.10.14
 * Time: 14:28
 */

namespace Cundd\PersistentObjectStore\Server\ValueObject;


use Cundd\PersistentObjectStore\Server\Handler\HandlerResultInterface;

/**
 * Handler result implementation
 *
 * @package Cundd\PersistentObjectStore\Server\ValueObject
 */
class MutableHandlerResult extends AbstractHandlerResult implements HandlerResultInterface
{
    /**
     * Sets the status code for the response
     *
     * @param int $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * Sets the request's response data
     *
     * @param mixed $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

}
