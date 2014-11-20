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
 * Handler result implementation
 *
 * @package Cundd\PersistentObjectStore\Server\ValueObject
 */
class HandlerResult implements HandlerResultInterface, Immutable
{
    /**
     * @var integer
     */
    protected $statusCode;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * Creates a new result with the given data and status
     *
     * @param integer $statusCode
     * @param mixed   $data
     */
    function __construct($statusCode, $data = null)
    {
        $this->statusCode = $statusCode;
        $this->data       = $data;
    }


    /**
     * Returns the requests response data
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Returns the status code for the response
     *
     * @return integer
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }


} 