<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Server\ValueObject;

/**
 * Test for Deferred Result
 */
class DeferredResultTest extends \PHPUnit\Framework\TestCase
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
