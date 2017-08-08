<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server\Session;

use Cundd\Stairtower\Server\Controller\MutableControllerResultInterface;
use Cundd\Stairtower\Server\Cookie\Constants as CookieConstants;
use Cundd\Stairtower\Server\Cookie\Cookie;
use Cundd\Stairtower\Server\Session\Constants as SessionConstants;
use Cundd\Stairtower\Server\ValueObject\MutableControllerResult;
use Cundd\Stairtower\Server\ValueObject\RequestInterface;

/**
 * A trait to provide a controller with the ability to load a session
 */
trait SessionControllerTrait
{
    /**
     * Session provider instance
     *
     * @var \Cundd\Stairtower\Server\Session\SessionProviderInterface
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
     * Returns a mutable Controller Result instance
     *
     * This method may be used to inject session cookies into the response. The arguments are directly passed to the
     * result's constructor.
     *
     * @param integer $statusCode
     * @param mixed   $data
     * @param string  $contentType
     * @param array   $headers
     * @return MutableControllerResultInterface
     */
    public function buildResponse(int $statusCode = 0, $data = null, string $contentType = '', array $headers = [])
    {
        $response = new MutableControllerResult($statusCode, $data, $contentType, $headers);
        $sessionCookie = new Cookie(SessionConstants::SESSION_ID_COOKIE_NAME, $this->getSession()->getIdentifier());

        $response->setHeaders(
            array_merge(
                $response->getHeaders(),
                [CookieConstants::SET_COOKIE_HEADER_NAME => $sessionCookie->toHeader()]
            )
        );
        //$response->addHeader(
        //    CookieConstants::SET_COOKIE_HEADER_NAME,
        //    $sessionCookie->toHeader()
        //);

        return $response;
    }

    /**
     * Returns the current Request Info instance
     *
     * @return RequestInterface
     */
    abstract public function getRequest(): ?RequestInterface;
}
