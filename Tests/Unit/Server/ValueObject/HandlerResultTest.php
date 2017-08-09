<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Tests\Unit\Server\ValueObject;

use Cundd\Stairtower\Server\ValueObject\HandlerResult;
use PHPUnit\Framework\TestCase;

/**
 * Test for Handler Results
 */
class HandlerResultTest extends TestCase
{
    /**
     * @var HandlerResult
     */
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = new HandlerResult(200, 'my data');
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
}
