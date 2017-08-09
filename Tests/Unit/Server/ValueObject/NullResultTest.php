<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Tests\Unit\Server\ValueObject;

use Cundd\Stairtower\Server\ValueObject\NullResult;
use PHPUnit\Framework\TestCase;

/**
 * Test for Null Result
 */
class NullResultTest extends TestCase
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
