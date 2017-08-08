<?php
declare(strict_types=1);

namespace Cundd\Stairtower\ErrorHandling;

use stdClass;

class TestClass_ForErrorHandler
{

}

/**
 * Test for ErrorHandler
 */
class ErrorHandlerTest extends \PHPUnit\Framework\TestCase
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
     * @expectedException \Cundd\Stairtower\Exception\StringTransformationException
     */
    public function stdClassToStringTest()
    {
        $object = new stdClass();

        return (string)$object;
    }

    /**
     * @test
     * @expectedException \Cundd\Stairtower\Exception\StringTransformationException
     */
    public function documentToStringTest()
    {
        $object = new TestClass_ForErrorHandler();

        return (string)$object;
    }
}
