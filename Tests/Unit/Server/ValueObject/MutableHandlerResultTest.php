<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 02.04.15
 * Time: 20:48
 */

namespace Cundd\PersistentObjectStore\Server\ValueObject;

/**
 * Test for mutable Handler Results
 *
 * @package Cundd\PersistentObjectStore\Server\ValueObject
 */
class MutableHandlerResultTest extends \PHPUnit_Framework_TestCase
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
    public function setDataTest(){
        $newData = 'new data';
        $this->fixture->setData($newData);
        $this->assertSame($newData, $this->fixture->getData());
    }
}
