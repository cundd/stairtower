<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 05.04.15
 * Time: 18:55
 */

namespace Cundd\PersistentObjectStore\Server\Session;

use Cundd\PersistentObjectStore\Server\ValueObject\Request;

/**
 * A trait to provide a controller with the ability to load a session
 *
 * @package Cundd\PersistentObjectStore\Server\Session
 */
trait SessionControllerTrait {
    /**
     * Session provider instance
     *
     * @var \Cundd\PersistentObjectStore\Server\Session\SessionProviderInterface
     * @Inject
     */
    protected $sessionProvider;

    /**
     * Returns the session loaded for the current request, or newly created one
     *
     * @return SessionInterface|null
     */
    public function getSession()
    {
        $session = $this->sessionProvider->loadForRequest($this->getRequest());
        if (!$session) {
            $session = $this->sessionProvider->create();
        }
        return $session;
    }

    /**
     * Returns the current Request Info instance
     *
     * @return Request
     */
    abstract public function getRequest();
}
