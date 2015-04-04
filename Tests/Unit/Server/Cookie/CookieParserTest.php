<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 03.04.15
 * Time: 19:02
 */

namespace Cundd\PersistentObjectStore\Server\Cookie;

use Cundd\PersistentObjectStore\AbstractCase;
use Cundd\PersistentObjectStore\Server\ValueObject\RequestInterface;
use stdClass;

/**
 * Test for cookie parsers
 *
 * @package Cundd\PersistentObjectStore\Server\Cookie
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
        $request = $this->getMockForAbstractClass('Cundd\\PersistentObjectStore\\Server\\ValueObject\\RequestInterface');
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
     * @expectedException \Cundd\PersistentObjectStore\Exception\InvalidArgumentError
     */
    public function invalidArgumentShouldFailTest()
    {
        $this->fixture->parse(new stdClass());
    }
}
