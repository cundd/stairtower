<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 04.04.15
 * Time: 14:39
 */

namespace Cundd\PersistentObjectStore\Server\Session;

use Cundd\PersistentObjectStore\Server\ValueObject\RequestInterface;

/**
 * Interface for classes that allow creation and loading of session objects
 *
 * @package Cundd\PersistentObjectStore\Server\Session
 */
interface SessionProviderInterface
{
    /**
     * Create (start) a new session
     *
     * @param string $sessionId Optional session ID to use. If none is given it will be generated
     * @return SessionInterface
     */
    public function create($sessionId = null);

    /**
     * Loads the session with the given session ID
     *
     * @param string $sessionId
     * @return SessionInterface|null
     */
    public function load($sessionId);

    /**
     * Loads the session for the given request
     *
     * @param RequestInterface $request
     * @return SessionInterface|null
     */
    public function loadForRequest(RequestInterface $request);
}
