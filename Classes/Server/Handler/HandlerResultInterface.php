<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 10.10.14
 * Time: 17:59
 */
namespace Cundd\PersistentObjectStore\Server\Handler;

/**
 * Interface for classes that describe a Handlers response
 *
 * @package Cundd\PersistentObjectStore\Server\Handler
 */
interface HandlerResultInterface
{
    /**
     * Returns the status code for the response
     *
     * @return integer
     */
    public function getStatusCode();

    /**
     * Returns the requests response data
     *
     * @return mixed
     */
    public function getData();
}
