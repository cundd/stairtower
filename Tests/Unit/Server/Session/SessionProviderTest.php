<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server\Session;


use Cundd\Stairtower\Server\Cookie\Cookie;
use Cundd\Stairtower\Server\ValueObject\RequestInterface;

/**
 * Test class for the Session Provider
 */
class SessionProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var SessionProviderInterface
     */
    protected $fixture;

    protected function setUp()
    {
        parent::setUp();
        $this->fixture = new SessionProvider();
    }

    /**
     * @test
     */
    public function createWithSessionIdTest()
    {
        $sessionId = 'my-session';
        $session = $this->fixture->create($sessionId);
        $this->assertNotNull($session);
        $this->assertSame($sessionId, $session->getIdentifier());
    }

    /**
     * @test
     * @expectedException \Cundd\Stairtower\Server\Session\Exception\InvalidSessionIdentifierException
     */
    public function createExistingSessionShouldFailTest()
    {
        $sessionId = 'my-new-session-' . __METHOD__ . time();
        $session = $this->fixture->create($sessionId);
        $this->assertNotNull($session);

        $this->fixture->create($sessionId);
    }

    /**
     * @test
     */
    public function createWithoutSessionIdTest()
    {
        $session = $this->fixture->create();
        $this->assertNotNull($session);
        $this->assertNotEmpty($session->getIdentifier());
    }

    /**
     * @test
     */
    public function loadTest()
    {
        $sessionId = 'my-new-session';
        $createdSession = $this->fixture->create($sessionId);


        $loadedSession = $this->fixture->load($sessionId);
        $this->assertNotNull($loadedSession);
        $this->assertSame($loadedSession, $createdSession);
    }

    /**
     * @test
     */
    public function loadNotExistingTest()
    {
        $sessionId = 'not-existing-session-' . time();
        $this->assertNull($this->fixture->load($sessionId));
    }

    /**
     * @test
     */
    public function loadForRequestTest()
    {
        $sessionId = 'my-new-session-' . __METHOD__ . time();
        $createdSession = $this->fixture->create($sessionId);


        /** @var RequestInterface $request */
        $request = $this->getMockForAbstractClass(RequestInterface::class);
        $request
            ->expects($this->any())
            ->method('getCookie')
            ->will(
                $this->returnValue(new Cookie(Constants::SESSION_ID_COOKIE_NAME, $sessionId))
            );

        $loadedSession = $this->fixture->loadForRequest($request);
        $this->assertNotNull($loadedSession);
        $this->assertSame($loadedSession, $createdSession);
    }

    /**
     * @test
     */
    public function loadForRequestNotExistingTest()
    {
        $sessionId = 'not-existing-session-' . time();

        /** @var RequestInterface $request */
        $request = $this->getMockForAbstractClass(RequestInterface::class);
        $request
            ->expects($this->any())
            ->method('getCookie')
            ->will(
                $this->returnValue(new Cookie(Constants::SESSION_ID_COOKIE_NAME, $sessionId))
            );

        $this->assertNull($this->fixture->loadForRequest($request));
    }
}
