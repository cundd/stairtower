<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 05.04.15
 * Time: 21:44
 */

namespace Cundd\PersistentObjectStore\Server\Session;


use Cundd\PersistentObjectStore\AbstractCase;
use Cundd\PersistentObjectStore\Server\Cookie\Constants as CookieConstants;
use Cundd\PersistentObjectStore\Server\Session\Constants as SessionConstants;
use Cundd\PersistentObjectStore\Server\Cookie\Cookie;
use Cundd\PersistentObjectStore\Server\ValueObject\RequestInterface;
use Test_Session_Controller;

/**
 * Tests for the Session Controller trait
 *
 * @package Cundd\PersistentObjectStore\Server\Session
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
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Server\\Session\\SessionInterface', $session);
    }

    /**
     * @test
     */
    public function buildResponseTest()
    {
        $response = $this->fixture->buildResponse();
        $this->assertNotNull($response);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Server\\Controller\\MutableControllerResultInterface', $response);

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
        $headers = array(
            'my-header' => 'my-header-value'
        );
        $response = $this->fixture->buildResponse($statusCode, $data, $contentType, $headers);
        $this->assertNotNull($response);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Server\\Controller\\MutableControllerResultInterface', $response);

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

        /** @var RequestInterface $request */
        $request = $this->getMockForAbstractClass('Cundd\\PersistentObjectStore\\Server\\ValueObject\\RequestInterface');
        $request
            ->expects($this->any())
            ->method('getCookie')
            ->will(
                $this->returnValue(new Cookie(SessionConstants::SESSION_ID_COOKIE_NAME, $this->sessionId))
            );

        /** @var Test_Session_Controller $fixture */
        $fixture = $this->getDiContainer()->get('Test_Session_Controller');
        $fixture->setRequest($request);
        $this->fixture = $fixture;
    }
}
