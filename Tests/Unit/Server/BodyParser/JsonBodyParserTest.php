<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Tests\Unit\Server\BodyParser;

use Cundd\Stairtower\Server\BodyParser\BodyParserInterface;
use Cundd\Stairtower\Server\ValueObject\RequestInterface;
use Cundd\Stairtower\Tests\Unit\AbstractCase;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\UriInterface;

/**
 * JSON based body parser
 */
class JsonBodyParserTest extends AbstractCase
{
    /**
     * @var BodyParserInterface
     */
    protected $fixture;

    /**
     * @test
     */
    public function parseBodyTest()
    {
        $dummyRequest = $this->buildDummyRequest();

        $this->assertArrayHasKey('email', $this->fixture->parse('{"email":"info@cundd.net"}', $dummyRequest));
        $this->assertArrayHasKey(
            'email',
            $this->fixture->parse('{"email":"info@cundd.net","name":"Daniel"}', $dummyRequest)
        );
        $this->assertArrayHasKey(
            'email',
            $this->fixture->parse('{"name":"Daniel","email":"info@cundd.net"}', $dummyRequest)
        );


        $testContent = [
            [
                'email' => 'info@cundd.net',
                'name'  => 'Daniel',
            ],
            [
                'email' => 'spm@cundd.net',
                'name'  => 'Superman',
            ],
        ];
        $this->assertEquals($testContent, $this->fixture->parse(json_encode($testContent), $dummyRequest));
    }

    /**
     * @test
     * @expectedException \Cundd\Stairtower\Server\Exception\InvalidBodyException
     */
    public function parseInvalidBodyTest()
    {
        $dummyRequest = $this->buildDummyRequest();
        $this->fixture->parse('name":"Daniel","email":"info@cundd.net"}', $dummyRequest);
    }

    /**
     * @test
     */
    public function parseEmptyBodyTest()
    {
        /** @var RequestInterface $dummyRequest */
        $dummyRequest = $this->buildDummyRequest();
        $this->assertNull($this->fixture->parse('', $dummyRequest));
    }

    /**
     * @return RequestInterface
     */
    private function buildDummyRequest(): RequestInterface
    {
        /** @var ObjectProphecy|RequestInterface $prophecy */
        $prophecy = $this->prophesize(RequestInterface::class);
        $path = '/contacts/';
        $prophecy->getPath()->willReturn($path);

        /** @var ObjectProphecy|UriInterface $uri */
        $uri = $this->prophesize(UriInterface::class);
        $uri->getPath()->willReturn($path);

        $prophecy->getUri()->willReturn($uri->reveal());

        $prophecy->getMethod()->willReturn('GET');
        /** @var RequestInterface $dummyRequest */
        $dummyRequest = $prophecy->reveal();

        return $dummyRequest;
    }
}
 