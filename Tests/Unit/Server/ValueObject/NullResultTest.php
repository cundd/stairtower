<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Server\ValueObject;

/**
 * Test for Null Result
 */
class NullResultTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var NullResult
     */
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = new NullResult();
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
