<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 02.04.15
 * Time: 20:48
 */

namespace Cundd\PersistentObjectStore\Server\ValueObject;

/**
 * Test for Raw Result
 *
 * @package Cundd\PersistentObjectStore\Server\ValueObject
 */
class RawResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DeferredResult
     */
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = new RawResult(200, 'my data');
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
