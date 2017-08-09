<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Tests\Unit\Server\ValueObject;

use Cundd\Stairtower\Server\ValueObject\DeferredResult;
use PHPUnit\Framework\TestCase;

/**
 * Test for Deferred Result
 */
class DeferredResultTest extends TestCase
{
    /**
     * @var DeferredResult
     */
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = new DeferredResult();
        parent::setUp();
    }

    /**
     * @test
     */
    public function getStatusCodeTest()
    {
        $this->assertSame(204, $this->fixture->getStatusCode());
    }

    /**
     * @test
     */
    public function getDataTest()
    {
        $this->assertSame(null, $this->fixture->getData());
    }
}
