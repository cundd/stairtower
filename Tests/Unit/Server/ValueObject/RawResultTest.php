<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Tests\Unit\Server\ValueObject;

use Cundd\Stairtower\Server\ValueObject\RawResult;
use PHPUnit\Framework\TestCase;

/**
 * Test for Raw Result
 */
class RawResultTest extends TestCase
{
    /**
     * @var RawResult
     */
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = new RawResult(200, 'my data', 'text/plain');
        parent::setUp();
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

    /**
     * @test
     */
    public function getContentTypeTest()
    {
        $this->assertSame('text/plain', $this->fixture->getContentType());
    }

    /**
     * @test
     */
    public function getContentTypeDefaultTest()
    {
        $this->fixture = new RawResult(200, 'my data');
        $this->assertSame('application/octet-stream', $this->fixture->getContentType());
    }
}
