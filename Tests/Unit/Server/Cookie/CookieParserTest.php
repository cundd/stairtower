<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Tests\Unit\Server\Cookie;

use Cundd\Stairtower\Server\Cookie\CookieParserInterface;
use Cundd\Stairtower\Server\ValueObject\RequestInterface;
use Cundd\Stairtower\Tests\Unit\AbstractCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
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
        /** @var RequestInterface|ObjectProphecy $requestProphecy */
        $requestProphecy = $this->prophesize(RequestInterface::class);
        /** @var string $stringArgument */
        $stringArgument = Argument::type('string');
        $requestProphecy->getHeader($stringArgument)->willReturn(['user=daniel; last-request=2015-04-02+21%3A04%3A12']);
        /** @var RequestInterface $request */
        $request = $requestProphecy->reveal();

        $cookies = $this->fixture->parse($request);
        $this->assertNotEmpty($cookies);
        $this->assertArrayHasKey('user', $cookies);

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
