<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Tests\Unit\Server\Cookie;

use Cundd\Stairtower\Server\Cookie\Cookie;

/**
 * Test for Cookies
 */
class CookieTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Cookie
     */
    protected $fixture;


    /**
     * @test
     */
    public function fullCookieTest()
    {
        $this->assertSame(
            'user=daniel; Path=/home; Expires=Thu, 02 Apr 2015 22:00:00 GMT; Domain=.example.com; Secure; HttpOnly',
            (string)new Cookie(
                'user',
                'daniel',
                new \DateTime('Thu, 02 Apr 2015 23:00:00 +0100'),
                '/home',
                '.example.com',
                true,
                true
            )
        );
    }

    /**
     * @test
     */
    public function cookieWithoutHttpOnlyTest()
    {
        $this->assertSame(
            'user=daniel; Path=/home; Expires=Thu, 02 Apr 2015 22:00:00 GMT; Domain=.example.com; Secure',
            (string)new Cookie(
                'user',
                'daniel',
                new \DateTime('Thu, 02 Apr 2015 23:00:00 +0100'),
                '/home',
                '.example.com',
                true
            )
        );
    }

    /**
     * @test
     */
    public function cookieWithoutSecureTest()
    {
        $this->assertSame(
            'user=daniel; Path=/home; Expires=Thu, 02 Apr 2015 22:00:00 GMT; Domain=.example.com; HttpOnly',
            (string)new Cookie(
                'user',
                'daniel',
                new \DateTime('Thu, 02 Apr 2015 23:00:00 +0100'),
                '/home',
                '.example.com',
                false,
                true
            )
        );
    }

    /**
     * @test
     */
    public function cookieWithoutDomainTest()
    {
        $this->assertSame(
            'user=daniel; Path=/home; Expires=Thu, 02 Apr 2015 22:00:00 GMT; Secure; HttpOnly',
            (string)new Cookie(
                'user',
                'daniel',
                new \DateTime('Thu, 02 Apr 2015 23:00:00 +0100'),
                '/home',
                null,
                true,
                true
            )
        );
    }

    /**
     * @test
     */
    public function cookieWithoutPathTest()
    {
        $this->assertSame(
            'user=daniel; Expires=Thu, 02 Apr 2015 22:00:00 GMT; Domain=.example.com; Secure; HttpOnly',
            (string)new Cookie(
                'user',
                'daniel',
                new \DateTime('Thu, 02 Apr 2015 23:00:00 +0100'),
                null,
                '.example.com',
                true,
                true
            )
        );
    }

    /**
     * @test
     */
    public function cookieWithoutExpirationTest()
    {
        $this->assertSame(
            'user=daniel; Path=/home; Domain=.example.com; Secure; HttpOnly',
            (string)new Cookie('user', 'daniel', null, '/home', '.example.com', true, true)
        );
    }

    /**
     * @test
     */
    public function cookieWithoutValueTest()
    {
        $this->assertSame(
            'user=; Path=/home; Expires=Thu, 02 Apr 2015 22:00:00 GMT; Domain=.example.com; Secure; HttpOnly',
            (string)new Cookie(
                'user',
                null,
                new \DateTime('Thu, 02 Apr 2015 23:00:00 +0100'),
                '/home',
                '.example.com',
                true,
                true
            )
        );
    }

    /**
     * @test
     * @expectedException \Cundd\Stairtower\Exception\InvalidArgumentError
     */
    public function cookieWithoutNameTest()
    {
        new Cookie(
            '', 'daniel', new \DateTime('Thu, 02 Apr 2015 23:00:00 +0100'), '/home', '.example.com', true, true
        );
    }

    /**
     * @test
     */
    public function fullCookieToHeaderTest()
    {
        $this->assertSame(
            'user=daniel; Path=/home; Expires=Thu, 02 Apr 2015 22:00:00 GMT; Domain=.example.com; Secure; HttpOnly',
            (new Cookie(
                'user',
                'daniel',
                new \DateTime('Thu, 02 Apr 2015 23:00:00 +0100'),
                '/home',
                '.example.com',
                true,
                true
            ))->toHeader()
        );
    }

    /**
     * @test
     */
    public function cookieWithoutHttpOnlyToHeaderTest()
    {
        $this->assertSame(
            'user=daniel; Path=/home; Expires=Thu, 02 Apr 2015 22:00:00 GMT; Domain=.example.com; Secure',
            (new Cookie(
                'user',
                'daniel',
                new \DateTime('Thu, 02 Apr 2015 23:00:00 +0100'),
                '/home',
                '.example.com',
                true
            ))->toHeader()
        );
    }

    /**
     * @test
     */
    public function cookieWithoutSecureToHeaderTest()
    {
        $this->assertSame(
            'user=daniel; Path=/home; Expires=Thu, 02 Apr 2015 22:00:00 GMT; Domain=.example.com; HttpOnly',
            (new Cookie(
                'user',
                'daniel',
                new \DateTime('Thu, 02 Apr 2015 23:00:00 +0100'),
                '/home',
                '.example.com',
                false,
                true
            ))->toHeader()
        );
    }

    /**
     * @test
     */
    public function cookieWithoutDomainToHeaderTest()
    {
        $this->assertSame(
            'user=daniel; Path=/home; Expires=Thu, 02 Apr 2015 22:00:00 GMT; Secure; HttpOnly',
            (new Cookie(
                'user', 'daniel', new \DateTime('Thu, 02 Apr 2015 23:00:00 +0100'), '/home', null, true, true
            ))->toHeader()
        );
    }

    /**
     * @test
     */
    public function cookieWithoutPathToHeaderTest()
    {
        $this->assertSame(
            'user=daniel; Expires=Thu, 02 Apr 2015 22:00:00 GMT; Domain=.example.com; Secure; HttpOnly',
            (new Cookie(
                'user',
                'daniel',
                new \DateTime('Thu, 02 Apr 2015 23:00:00 +0100'),
                null,
                '.example.com',
                true,
                true
            ))->toHeader()
        );
    }

    /**
     * @test
     */
    public function cookieWithoutExpirationToHeaderTest()
    {
        $this->assertSame(
            'user=daniel; Path=/home; Domain=.example.com; Secure; HttpOnly',
            (new Cookie('user', 'daniel', null, '/home', '.example.com', true, true))->toHeader()
        );
    }

    /**
     * @test
     */
    public function cookieWithoutValueToHeaderTest()
    {
        $this->assertSame(
            'user=; Path=/home; Expires=Thu, 02 Apr 2015 22:00:00 GMT; Domain=.example.com; Secure; HttpOnly',
            (new Cookie(
                'user',
                null,
                new \DateTime('Thu, 02 Apr 2015 23:00:00 +0100'),
                '/home',
                '.example.com',
                true,
                true
            ))->toHeader()
        );
    }

    /**
     * @test
     */
    public function urlEncodeTest()
    {
        $this->assertSame(
            'user=A+string+that+should+be+encoded.+Including+a+semicolon+%3B; Path=/home; Expires=Thu, 02 Apr 2015 22:00:00 GMT; Domain=.example.com; Secure; HttpOnly',
            (string)new Cookie(
                'user',
                'A string that should be encoded. Including a semicolon ;',
                new \DateTime('Thu, 02 Apr 2015 23:00:00 +0100'),
                '/home',
                '.example.com',
                true,
                true
            )
        );
    }

    /**
     * @test
     */
    public function doNotUrlEncodeTest()
    {
        $this->assertSame(
            'user=A string that should not be encoded. Including a semicolon ;; Path=/home; Expires=Thu, 02 Apr 2015 22:00:00 GMT; Domain=.example.com; Secure; HttpOnly',
            (string)new Cookie(
                'user',
                'A string that should not be encoded. Including a semicolon ;',
                new \DateTime('Thu, 02 Apr 2015 23:00:00 +0100'),
                '/home',
                '.example.com',
                true,
                true,
                false
            )
        );
    }
}
