<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 10.10.14
 * Time: 17:59
 */
namespace Cundd\PersistentObjectStore\Server\Controller;

use Cundd\PersistentObjectStore\Server\Handler\HandlerResultInterface;

/**
 * Interface for classes that describe a Handlers response
 *
 * @package Cundd\PersistentObjectStore\Server\Handler
 */
interface ControllerResultInterface extends HandlerResultInterface
{
    /**
     * Returns the content type of the request
     *
     * @return string
     */
    public function getContentType();
}