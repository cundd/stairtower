<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 05.04.15
 * Time: 21:44
 */

namespace Cundd\PersistentObjectStore\Server\Session;


use Cundd\PersistentObjectStore\AbstractCase;
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
                $this->returnValue(new Cookie(SessionProviderInterface::SESSION_ID_COOKIE_KEY, $this->sessionId))
            );

        /** @var Test_Session_Controller $fixture */
        $fixture = $this->getDiContainer()->get('Test_Session_Controller');
        $fixture->setRequest($request);
        $this->fixture = $fixture;
    }
}
