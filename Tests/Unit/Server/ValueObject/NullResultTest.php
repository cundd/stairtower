<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 02.04.15
 * Time: 20:48
 */

namespace Cundd\PersistentObjectStore\Server\ValueObject;

/**
 * Test for Null Result
 *
 * @package Cundd\PersistentObjectStore\Server\ValueObject
 */
class NullResultTest extends \PHPUnit_Framework_TestCase
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
