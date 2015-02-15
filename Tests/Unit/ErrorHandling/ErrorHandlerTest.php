<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 15.02.15
 * Time: 12:33
 */

namespace Cundd\PersistentObjectStore\ErrorHandling;

use Cundd\PersistentObjectStore\Domain\Model\Document;

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
        $object = new \stdClass();
        return (string)$object;
    }

    /**
     * @test
     * @expectedException \Cundd\PersistentObjectStore\Exception\StringTransformationException
     */
    public function documentToStringTest()
    {
        $object = new Document();
        return (string)$object;
    }
}
