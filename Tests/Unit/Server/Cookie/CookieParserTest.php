<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server\Cookie;

use Cundd\Stairtower\AbstractCase;
use Cundd\Stairtower\Server\ValueObject\RequestInterface;
use stdClass;

/**
 * Test for cookie parsers
 */
class CookieParserTest extends AbstractCase
{
    /**
     * @var CookieParserInterface
     */
    protected $fixture;

    /**
     * @test
     */
    public function parseTest()
    {
        /** @var RequestInterface $request */
        $request = $this->getMockForAbstractClass(RequestInterface::class);
        $request
            ->expects($this->any())
            ->method('getHeader')
            ->will($this->returnValue('user=daniel; last-request=2015-04-02+21%3A04%3A12'));

        $cookies = $this->fixture->parse($request);
        $this->assertNotEmpty($cookies);
        $this->arrayHasKey('user', $cookies);

        $firstCookie = $cookies['user'];
        $this->assertEquals('user', $firstCookie->getName());
        $this->assertEquals('daniel', $firstCookie->getValue());
        $this->assertEquals('/', $firstCookie->getPath());
    }

    /**
     * @test
     * @expectedException \Cundd\Stairtower\Exception\InvalidArgumentError
     */
    public function invalidArgumentShouldFailTest()
    {
        $this->fixture->parse(new stdClass());
    }
}
