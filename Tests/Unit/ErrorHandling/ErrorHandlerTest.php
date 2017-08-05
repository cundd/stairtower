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
        $this->markTestSkippedOnPhp7();
        $this->expectTestClass(array());
    }

    /**
     * @test
     * @expectedException \Cundd\PersistentObjectStore\Exception\InvalidArgumentError
     */
    public function expectTestClassGetNullTest()
    {
        $this->markTestSkippedOnPhp7();
        $this->expectTestClass(null);
    }

    /**
     * @test
     * @expectedException \Cundd\PersistentObjectStore\Exception\InvalidArgumentError
     */
    public function expectTestClassGetStdClassTest()
    {
        $this->markTestSkippedOnPhp7();
        $this->expectTestClass(new stdClass());
    }

    /**
     * @test
     * @expectedException \Cundd\PersistentObjectStore\Exception\InvalidArgumentError
     */
    public function expectArrayGetNullTest()
    {
        $this->markTestSkippedOnPhp7();
        $this->expectArray(null);
    }

    /**
     * @test
     * @expectedException \Cundd\PersistentObjectStore\Exception\InvalidArgumentError
     */
    public function expectArrayGetStdClassTest()
    {
        $this->markTestSkippedOnPhp7();
        $this->expectArray(new stdClass());
    }

    protected function expectTestClass(TestClass_ForErrorHandler $object)
    {
    }

    protected function expectArray(array $array)
    {
    }

    private function markTestSkippedOnPhp7()
    {
        if (version_compare(PHP_VERSION, '7.0.0') >= 0) {
            $this->markTestSkipped('Error handling changed in PHP 7 (http://php.net/manual/en/class.typeerror.php)');
        }
    }
}
