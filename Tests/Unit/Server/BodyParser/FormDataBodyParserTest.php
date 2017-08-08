<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server\BodyParser;

use Cundd\Stairtower\AbstractCase;
use Cundd\Stairtower\Server\ValueObject\RequestInterface;
use Prophecy\Prophecy\ObjectProphecy;

class FormDataBodyParserTest extends AbstractCase
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
        /** @var ObjectProphecy|RequestInterface $prophecy */
        $prophecy = $this->prophesize(RequestInterface::class);
        $prophecy->getPath()->willReturn('/contacts/');
        $prophecy->getMethod()->willReturn('GET');
        /** @var RequestInterface $dummyRequest */
        $dummyRequest = $prophecy->reveal();
        $this->assertArrayHasKey('email', $this->fixture->parse('email=test%40cundd.net&name=Daniel', $dummyRequest));
        $this->assertArrayHasKey('email', $this->fixture->parse('name=Daniel&email=test%40cundd.net', $dummyRequest));
        $this->assertArrayHasKey('email', $this->fixture->parse('email=test%40cundd.net', $dummyRequest));
    }
}
 