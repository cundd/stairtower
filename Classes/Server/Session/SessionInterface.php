<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 04.04.15
 * Time: 13:45
 */

namespace Cundd\PersistentObjectStore\Server\Session;

/**
 * Interface for session based classes
 *
 * @package Cundd\PersistentObjectStore\Server\Session
 */
interface SessionInterface
{
    /**
     * Returns the session identifier
     *
     * @return string
     */
    public function getIdentifier();
}
