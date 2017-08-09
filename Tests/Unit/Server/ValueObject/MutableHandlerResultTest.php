<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Tests\Unit\Server\ValueObject;

use Cundd\Stairtower\Server\ValueObject\MutableHandlerResult;
use PHPUnit\Framework\TestCase;

/**
 * Test for mutable Handler Results
 */
class MutableHandlerResultTest extends TestCase
{
    /**
     * @var MutableHandlerResult
     */
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = new MutableHandlerResult(200, 'my data');
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
