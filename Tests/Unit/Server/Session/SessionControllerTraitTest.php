<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Tests\Unit\Server\Session;


use Cundd\Stairtower\Server\Controller\MutableControllerResultInterface;
use Cundd\Stairtower\Server\Cookie\Constants as CookieConstants;
use Cundd\Stairtower\Server\Cookie\Cookie;
use Cundd\Stairtower\Server\Session\Constants as SessionConstants;
use Cundd\Stairtower\Server\Session\SessionControllerTrait;
use Cundd\Stairtower\Server\Session\SessionInterface;
use Cundd\Stairtower\Server\ValueObject\RequestInterface;
use Cundd\Stairtower\Tests\Fixtures\TestSessionController;
use Cundd\Stairtower\Tests\Unit\AbstractCase;

/**
 * Tests for the Session Controller trait
 */
class SessionControllerTraitTest extends AbstractCase
{
    /**
     * @var SessionControllerTrait
     */
    protected $fixture;

    /**
     * @var string
     */
    protected $sessionId = 'cundd_persistentobjectstore_server_session_sessioncontrollertraittest';

    /**
     * @test
     */
    public function getSessionTest()
    {
        $session = $this->fixture->getSession();
        $this->assertNotNull($session);
        $this->assertInstanceOf(SessionInterface::class, $session);
    }

    /**
     * @test
     */
    public function buildResponseTest()
    {
        $response = $this->fixture->buildResponse();
        $this->assertNotNull($response);
        $this->assertInstanceOf(
            MutableControllerResultInterface::class,
            $response
        );

        $headers = $response->getHeaders();
        $this->assertArrayHasKey(CookieConstants::SET_COOKIE_HEADER_NAME, $headers);
        $this->assertNotEmpty($headers[CookieConstants::SET_COOKIE_HEADER_NAME]);
    }

    /**
     * @test
     */
    public function buildResponseWithArgumentsTest()
    {
        $statusCode = 200;
        $data = 'my-data';
        $contentType = 'my-content-type';
        $headers = [
            'my-header' => 'my-header-value',
        ];
        $response = $this->fixture->buildResponse($statusCode, $data, $contentType, $headers);
        $this->assertNotNull($response);
        $this->assertInstanceOf(
            MutableControllerResultInterface::class,
            $response
        );

        $this->assertSame($statusCode, $response->getStatusCode());
        $this->assertSame($data, $response->getData());
        $this->assertSame($contentType . '; charset=utf-8', $response->getContentType());

        $headers = $response->getHeaders();
        $this->assertArrayHasKey(CookieConstants::SET_COOKIE_HEADER_NAME, $headers);
        $this->assertNotEmpty($headers[CookieConstants::SET_COOKIE_HEADER_NAME]);

        $this->assertArrayHasKey('my-header', $headers);
        $this->assertSame('my-header-value', $headers['my-header']);
    }

    protected function setUp()
    {
        parent::setUp();

        /** @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject $request */
        $request = $this->getMockForAbstractClass(RequestInterface::class);
        $request
            ->expects($this->any())
            ->method('getCookie')
            ->will(
                $this->returnValue(new Cookie(SessionConstants::SESSION_ID_COOKIE_NAME, $this->sessionId))
            );

        /** @var TestSessionController $fixture */
        $fixture = $this->getDiContainer()->get(TestSessionController::class);
        $fixture->setRequest($request);
        $this->fixture = $fixture;
    }
}
