<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 15.02.15
 * Time: 12:33
 */

namespace Cundd\PersistentObjectStore\ErrorHandling;

use stdClass;

class TestClass_ForErrorHandler
{

}

/**
 * Test for ErrorHandler
 *
 * @package Cundd\PersistentObjectStore\ErrorHandling
 */
class ErrorHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ErrorHandler
     */
    protected $fixture;

    protected function setUp()
    {
        parent::setUp();
        $this->fixture = new ErrorHandler();
        $this->fixture->register();
    }

    protected function tearDown()
    {
        unset($this->fixture);
        restore_error_handler();
        parent::tearDown();
    }

    /**
     * @test
     * @expectedException \Cundd\PersistentObjectStore\Exception\StringTransformationException
     */
    public function stdClassToStringTest()
    {
        $object = new stdClass();
        return (string)$object;
    }

    /**
     * @test
     * @expectedException \Cundd\PersistentObjectStore\Exception\StringTransformationException
     */
    public function documentToStringTest()
    {
        $object = new TestClass_ForErrorHandler();
        return (string)$object;
    }

    /**
     * @test
     * @expectedException \Cundd\PersistentObjectStore\Exception\InvalidArgumentError
     */
    public function expectTestClassGetArrayTest()
    {
        $this->expectTestClass(array());
    }

    /**
     * @test
     * @expectedException \Cundd\PersistentObjectStore\Exception\InvalidArgumentError
     */
    public function expectTestClassGetNullTest()
    {
        $this->expectTestClass(null);
    }

    /**
     * @test
     * @expectedException \Cundd\PersistentObjectStore\Exception\InvalidArgumentError
     */
    public function expectTestClassGetStdClassTest()
    {
        $this->expectTestClass(new stdClass());
    }

    /**
     * @test
     * @expectedException \Cundd\PersistentObjectStore\Exception\InvalidArgumentError
     */
    public function expectArrayGetNullTest()
    {
        $this->expectArray(null);
    }

    /**
     * @test
     * @expectedException \Cundd\PersistentObjectStore\Exception\InvalidArgumentError
     */
    public function expectArrayGetStdClassTest()
    {
        $this->expectArray(new stdClass());
    }

    protected function expectTestClass(TestClass_ForErrorHandler $object)
    {
    }

    protected function expectArray(array $array)
    {
    }
}
