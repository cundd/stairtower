<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Server\Session;


use Cundd\PersistentObjectStore\Exception\MissingExtensionException;
use Cundd\PersistentObjectStore\Memory\Manager;
use Cundd\PersistentObjectStore\Server\Session\Exception\InvalidSessionIdentifierException;
use Cundd\PersistentObjectStore\Server\ValueObject\RequestInterface;

/**
 * Session instance
 */
class SessionProvider implements SessionProviderInterface
{
    /**
     * Prefix for stored keys
     */
    const MEMORY_MANAGER_KEY_PREFIX = 'session_';

    /**
     * Tag for session objects
     */
    const MEMORY_MANAGER_TAG = 'session';

    public function create($sessionId = null): SessionInterface
    {
        if (!$sessionId) {
            $sessionId = $this->generateSessionId();
        } elseif ($this->load((string)$sessionId)) {
            throw new InvalidSessionIdentifierException(
                sprintf('Session with identifier %s already exists', $sessionId),
                1428151672
            );
        }

        $session = new Session($sessionId);
        Manager::registerObject($session, self::MEMORY_MANAGER_KEY_PREFIX . $sessionId, [self::MEMORY_MANAGER_TAG]);

        return $session;
    }

    /**
     * Loads the session with the given session ID
     *
     * @param string $sessionId
     * @return SessionInterface|null
     */
    public function load(string $sessionId): ?SessionInterface
    {
        $memoryManagerKey = self::MEMORY_MANAGER_KEY_PREFIX . $sessionId;
        if (!Manager::hasObject($memoryManagerKey)) {
            return null;
        }

        /** @var SessionInterface $session */
        $session = Manager::getObject($memoryManagerKey);

        return $session;
    }

    /**
     * Loads the session for the given request
     *
     * @param RequestInterface $request
     * @return SessionInterface|null
     */
    public function loadForRequest(RequestInterface $request):?SessionInterface
    {
        $cookie = $request->getCookie(Constants::SESSION_ID_COOKIE_NAME);
        if (!$cookie) {
            return null;
        }

        $sessionId = $cookie->getValue();

        return $this->load($sessionId);
    }

    /**
     * Generate a session ID
     *
     * @return string
     */
    protected function generateSessionId(): string
    {
        if (!is_callable('openssl_random_pseudo_bytes')) {
            throw new MissingExtensionException('OpenSSL is not enabled', 1428151048);
        }

        return bin2hex(openssl_random_pseudo_bytes(24));
    }
}
