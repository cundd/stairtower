<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 06.04.15
 * Time: 17:11
 */

namespace Cundd\PersistentObjectStore\Server\Session;


use Cundd\PersistentObjectStore\Server\Controller\MutableControllerResultInterface;

/**
 * Interface for Session based controllers
 *
 * @package Cundd\PersistentObjectStore\Server\Session
 */
interface SessionControllerInterface
{
    /**
     * Returns the session loaded for the current request, or newly created one
     *
     * @return SessionInterface|null
     */
    public function getSession();

    /**
     * Returns a mutable Controller Result instance
     *
     * This may be used to inject session cookies into the response
     *
     * @return MutableControllerResultInterface
     */
    public function buildResponse();
}