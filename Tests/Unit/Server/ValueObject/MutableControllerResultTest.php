<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 02.04.15
 * Time: 20:48
 */

namespace Cundd\PersistentObjectStore\Server\ValueObject;

use Cundd\PersistentObjectStore\Server\ContentType;

/**
 * Test for Controller Results
 *
 * @package Cundd\PersistentObjectStore\Server\ValueObject
 */
class MutableControllerResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MutableControllerResult
     */
    protected $fixture;

    /**
     * @var array
     */
    protected $headers = [
        'Content-Type' => 'this must be overwritten with the content type passed to the constructor',
    ];

    protected function setUp()
    {
        $this->fixture = new MutableControllerResult(200, 'my data', ContentType::JSON_APPLICATION, $this->headers);
        parent::setUp();
    }

    /**
     * @test
     */
    public function getContentTypeTest()
    {
        $this->assertSame(ContentType::JSON_APPLICATION . '; charset=utf-8', $this->fixture->getContentType());
    }

    /**
     * @test
     */
    public function getEmptyContentTypeTest()
    {
        $this->fixture = new ControllerResult(200, 'my data', null);
        $this->assertSame('', $this->fixture->getContentType());
    }


    /**
     * @test
     */
    public function getHeadersTest()
    {
        $this->assertInternalType('array', $this->fixture->getHeaders());
        $this->assertArrayHasKey('Content-Type', $this->fixture->getHeaders());
        $this->assertSame(
            ContentType::JSON_APPLICATION . '; charset=utf-8',
            $this->fixture->getHeaders()['Content-Type']
        );
    }


    /**
     * @test
     */
    public function getHeadersWithoutSetContentTypeTest()
    {
        $this->fixture = new ControllerResult(200, 'my data', null, [
            'Content-Type' => 'My content type'
        ]);

        $this->assertInternalType('array', $this->fixture->getHeaders());
        $this->assertArrayHasKey('Content-Type', $this->fixture->getHeaders());
        $this->assertSame(
            'My content type',
            $this->fixture->getHeaders()['Content-Type']
        );
    }

    /**
     * @test
     */
    public function setContentTypeTest()
    {
        $contentType = 'My content type';
        $this->fixture->setContentType($contentType);

        $this->assertInternalType('array', $this->fixture->getHeaders());
        $this->assertArrayHasKey('Content-Type', $this->fixture->getHeaders());
        $this->assertSame($contentType . '; charset=utf-8', $this->fixture->getHeaders()['Content-Type']);
    }

    /**
     * @test
     */
    public function setHeadersTest()
    {
        $this->assertInternalType('array', $this->fixture->getHeaders());
        $this->assertArrayHasKey('Content-Type', $this->fixture->getHeaders());
        $this->assertSame(
            ContentType::JSON_APPLICATION . '; charset=utf-8',
            $this->fixture->getHeaders()['Content-Type']
        );

        $headers = array(
            'Test-Header' => 'my test header value'
        );
        $this->fixture->setHeaders($headers);

        $this->assertInternalType('array', $this->fixture->getHeaders());
        $this->assertArrayHasKey('Content-Type', $this->fixture->getHeaders());
        $this->assertSame(
            ContentType::JSON_APPLICATION . '; charset=utf-8',
            $this->fixture->getHeaders()['Content-Type']
        );

        $this->assertArrayHasKey('Content-Type', $this->fixture->getHeaders());
        $this->assertSame('my test header value', $this->fixture->getHeaders()['Test-Header']);
    }

    /**
     * @test
     */
    public function addHeaderTest()
    {
        $this->assertInternalType('array', $this->fixture->getHeaders());
        $this->assertArrayHasKey('Content-Type', $this->fixture->getHeaders());
        $this->assertSame(
            ContentType::JSON_APPLICATION . '; charset=utf-8',
            $this->fixture->getHeaders()['Content-Type']
        );

        $this->fixture->addHeader('Test-Header', 'my test header value');

        $this->assertInternalType('array', $this->fixture->getHeaders());
        $this->assertArrayHasKey('Content-Type', $this->fixture->getHeaders());
        $this->assertSame(
            ContentType::JSON_APPLICATION . '; charset=utf-8',
            $this->fixture->getHeaders()['Content-Type']
        );

        $this->assertArrayHasKey('Content-Type', $this->fixture->getHeaders());
        $this->assertSame('my test header value', $this->fixture->getHeaders()['Test-Header']);
    }

    /**
     * @test
     */
    public function addExistingHeaderTest()
    {
        $this->assertInternalType('array', $this->fixture->getHeaders());
        $this->assertArrayNotHasKey('Set-Cookie', $this->fixture->getHeaders());

        $this->fixture->addHeader('Set-Cookie', 'cookie 1');
        $this->fixture->addHeader('Set-Cookie', 'cookie 2');

        $this->assertInternalType('array', $this->fixture->getHeaders());
        $this->assertArrayHasKey('Set-Cookie', $this->fixture->getHeaders());
        $this->assertSame(
            ['cookie 1', 'cookie 2'],
            $this->fixture->getHeaders()['Set-Cookie']
        );
    }

    /**
     * @test
     */
    public function replaceHeaderTest()
    {
        $this->assertInternalType('array', $this->fixture->getHeaders());
        $this->assertArrayNotHasKey('Set-Cookie', $this->fixture->getHeaders());

        $this->fixture->replaceHeader('Set-Cookie', 'cookie 1');
        $this->fixture->replaceHeader('Set-Cookie', 'cookie 2');

        $this->assertInternalType('array', $this->fixture->getHeaders());
        $this->assertArrayHasKey('Set-Cookie', $this->fixture->getHeaders());
        $this->assertSame('cookie 2', $this->fixture->getHeaders()['Set-Cookie']);
    }

    /**
     * @test
     */
    public function getStatusCodeTest()
    {
        $this->assertSame(200, $this->fixture->getStatusCode());
    }

    /**
     * @test
     */
    public function setStatusCodeTest()
    {
        $statusCode = 404;
        $this->fixture->setStatusCode($statusCode);
        $this->assertSame($statusCode, $this->fixture->getStatusCode());
    }

    /**
     * @test
     */
    public function getDataTest()
    {
        $this->assertSame('my data', $this->fixture->getData());
    }

    /**
     * @test
     */
    public function setDataTest()
    {
        $newData = 'new data';
        $this->fixture->setData($newData);
        $this->assertSame($newData, $this->fixture->getData());
    }
}
