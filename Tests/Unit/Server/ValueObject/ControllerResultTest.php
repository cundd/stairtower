<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Server\ValueObject;

use Cundd\PersistentObjectStore\Server\ContentType;

/**
 * Test for Controller Results
 */
class ControllerResultTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ControllerResult
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
        $this->fixture = new ControllerResult(200, 'my data', ContentType::JSON_APPLICATION, $this->headers);
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
    public function getDefaultContentTypeTest()
    {
        $this->fixture = new ControllerResult(200, 'my data');
        $this->assertSame(ContentType::HTML_TEXT . '; charset=utf-8', $this->fixture->getContentType());
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
        $this->fixture = new ControllerResult(
            200, 'my data', null, [
                'Content-Type' => 'My content type',
            ]
        );

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
    public function getStatusCodeTest()
    {
        $this->assertSame(200, $this->fixture->getStatusCode());
    }

    /**
     * @test
     */
    public function getDataTest()
    {
        $this->assertSame('my data', $this->fixture->getData());
    }
}
