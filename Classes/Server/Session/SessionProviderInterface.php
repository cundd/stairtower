<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server\Session;

use Cundd\Stairtower\Server\ValueObject\RequestInterface;

/**
 * Interface for classes that allow creation and loading of session objects
 */
interface SessionProviderInterface
{
    /**
     * Create (start) a new session
     *
     * @param string $sessionId Optional session ID to use. If none is given it will be generated
     * @return SessionInterface
     */
    public function create($sessionId = null):SessionInterface;

    /**
     * Loads the session with the given session ID
     *
     * @param string $sessionId
     * @return SessionInterface|null
     */
    public function load(string $sessionId):?SessionInterface;

    /**
     * Loads the session for the given request
     *
     * @param RequestInterface $request
     * @return SessionInterface|null
     */
    public function loadForRequest(RequestInterface $request):?SessionInterface;
}
