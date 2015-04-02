<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 02.04.15
 * Time: 20:48
 */

namespace Cundd\PersistentObjectStore\Server\ValueObject;

/**
 * Test for Cookies
 *
 * @package Cundd\PersistentObjectStore\Server\ValueObject
 */
class CookieTest extends \PHPUnit_Framework_TestCase
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
            'user=daniel; Path=/home; Expires=Thu, 02 Apr 2015 22:00:00 +0000; Domain=.example.com; Secure; HttpOnly',
            (string) new Cookie('user', 'daniel', new \DateTime('tomorrow'), '/home', '.example.com', true, true)
        );
    }

    /**
     * @test
     */
    public function cookieWithoutHttpOnlyTest()
    {
        $this->assertSame(
            'user=daniel; Path=/home; Expires=Thu, 02 Apr 2015 22:00:00 +0000; Domain=.example.com; Secure',
            (string) new Cookie('user', 'daniel', new \DateTime('tomorrow'), '/home', '.example.com', true)
        );
    }

    /**
     * @test
     */
    public function cookieWithoutSecureTest()
    {
        $this->assertSame(
            'user=daniel; Path=/home; Expires=Thu, 02 Apr 2015 22:00:00 +0000; Domain=.example.com; HttpOnly',
            (string) new Cookie('user', 'daniel', new \DateTime('tomorrow'), '/home', '.example.com', false, true)
        );
    }

    /**
     * @test
     */
    public function cookieWithoutDomainTest()
    {
        $this->assertSame(
            'user=daniel; Path=/home; Expires=Thu, 02 Apr 2015 22:00:00 +0000; Secure; HttpOnly',
            (string) new Cookie('user', 'daniel', new \DateTime('tomorrow'), '/home', null, true, true)
        );
    }

    /**
     * @test
     */
    public function cookieWithoutPathTest()
    {
        $this->assertSame(
            'user=daniel; Expires=Thu, 02 Apr 2015 22:00:00 +0000; Domain=.example.com; Secure; HttpOnly',
            (string) new Cookie('user', 'daniel', new \DateTime('tomorrow'), null, '.example.com', true, true)
        );
    }

    /**
     * @test
     */
    public function cookieWithoutExpirationTest()
    {
        $this->assertSame(
            'user=daniel; Path=/home; Domain=.example.com; Secure; HttpOnly',
            (string) new Cookie('user', 'daniel', null, '/home', '.example.com', true, true)
        );
    }

    /**
     * @test
     */
    public function cookieWithoutValueTest()
    {
        $this->assertSame(
            'user=; Path=/home; Expires=Thu, 02 Apr 2015 22:00:00 +0000; Domain=.example.com; Secure; HttpOnly',
            (string) new Cookie('user', null, new \DateTime('tomorrow'), '/home', '.example.com', true, true)
        );
    }

    /**
     * @test
     * @expectedException \Cundd\PersistentObjectStore\Exception\InvalidArgumentError
     */
    public function cookieWithoutNameTest()
    {
        new Cookie(null, 'daniel', new \DateTime('tomorrow'), '/home', '.example.com', true, true);
    }






    /**
     * @test
     */
    public function fullCookieToHeaderTest()
    {
        $this->assertSame(
            'user=daniel; Path=/home; Expires=Thu, 02 Apr 2015 22:00:00 +0000; Domain=.example.com; Secure; HttpOnly',
            (new Cookie('user', 'daniel', new \DateTime('tomorrow'), '/home', '.example.com', true, true))->toHeader()
        );
    }

    /**
     * @test
     */
    public function cookieWithoutHttpOnlyToHeaderTest()
    {
        $this->assertSame(
            'user=daniel; Path=/home; Expires=Thu, 02 Apr 2015 22:00:00 +0000; Domain=.example.com; Secure',
            (new Cookie('user', 'daniel', new \DateTime('tomorrow'), '/home', '.example.com', true))->toHeader()
        );
    }

    /**
     * @test
     */
    public function cookieWithoutSecureToHeaderTest()
    {
        $this->assertSame(
            'user=daniel; Path=/home; Expires=Thu, 02 Apr 2015 22:00:00 +0000; Domain=.example.com; HttpOnly',
            (new Cookie('user', 'daniel', new \DateTime('tomorrow'), '/home', '.example.com', false, true))->toHeader()
        );
    }

    /**
     * @test
     */
    public function cookieWithoutDomainToHeaderTest()
    {
        $this->assertSame(
            'user=daniel; Path=/home; Expires=Thu, 02 Apr 2015 22:00:00 +0000; Secure; HttpOnly',
            (new Cookie('user', 'daniel', new \DateTime('tomorrow'), '/home', null, true, true))->toHeader()
        );
    }

    /**
     * @test
     */
    public function cookieWithoutPathToHeaderTest()
    {
        $this->assertSame(
            'user=daniel; Expires=Thu, 02 Apr 2015 22:00:00 +0000; Domain=.example.com; Secure; HttpOnly',
            (new Cookie('user', 'daniel', new \DateTime('tomorrow'), null, '.example.com', true, true))->toHeader()
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
            'user=; Path=/home; Expires=Thu, 02 Apr 2015 22:00:00 +0000; Domain=.example.com; Secure; HttpOnly',
            (new Cookie('user', null, new \DateTime('tomorrow'), '/home', '.example.com', true, true))->toHeader()
        );
    }
}
