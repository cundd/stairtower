<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 08.01.15
 * Time: 11:08
 */

namespace Server\Controller;

use Cundd\PersistentObjectStore\Server\Controller\ControllerInterface;
use Cundd\PersistentObjectStore\Server\ValueObject\ControllerResult;
use Cundd\PersistentObjectStore\Server\ValueObject\RequestInfoFactory;
use React\Http\Request;
use React\Http\Response;
use React_ConnectionStub;

/**
 * Tests for the abstract Controller implementation
 *
 * @package Server\Controller
 */
class AbstractControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ControllerInterface
     */
    protected $fixture;

    protected function setUp()
    {
        parent::setUp();

        $this->fixture = $this->getMockForAbstractClass('Cundd\\PersistentObjectStore\\Server\\Controller\\AbstractController');

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(
            new Request('GET', '/_cundd-test-application/my_method')
        );
        $this->fixture->setRequest($requestInfo);
    }

    protected function tearDown()
    {
        $this->fixture->unsetRequest();
        unset($this->fixture);
        parent::tearDown();
    }

    /**
     * @test
     */
    public function initializeTest()
    {
        $this->fixture->initialize();
    }


    /**
     * @test
     */
    public function getRequestInfoTest()
    {
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Server\\ValueObject\\Request',
            $this->fixture->getRequest());
    }

    /**
     * @test
     */
    public function setRequestInfoTest()
    {
        $request     = new Request('GET', '/loaned/');
        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest($request);
        $this->fixture->setRequest($requestInfo);
        $this->assertSame($requestInfo, $this->fixture->getRequest());
    }


    /**
     * @test
     */
    public function unsetRequestInfoTest()
    {
        $request     = new Request('GET', '/loaned/');
        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest($request);
        $this->fixture->setRequest($requestInfo);

        $this->fixture->unsetRequest();
        $this->assertNull($this->fixture->getRequest());
    }

    /**
     * @test
     */
    public function willInvokeActionTest()
    {
        $this->fixture->willInvokeAction('test');
    }

    /**
     * @test
     */
    public function didInvokeActionTest()
    {
        $result = new ControllerResult(200);
        $this->fixture->didInvokeAction('test', $result);
    }

    /**
     * @test
     */
    public function processRequestTest()
    {

        /** @var ControllerInterface $controller */
        $controller = $this->getMockBuilder('Cundd\\PersistentObjectStore\\Server\\Controller\\AbstractController')
            ->setMethods(array('getHelloAction'))
            ->getMock();
        $controller
            ->expects($this->any())
            ->method('getHelloAction')
            ->will($this->returnValue(true));

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(
            new Request('GET', '/_cundd-test-application/hello')
        );
        $response = new Response(new React_ConnectionStub());

        /** @var \Cundd\PersistentObjectStore\Server\ValueObject\ControllerResult $result */
        $result = $controller->processRequest($requestInfo, $response);
        $this->assertNotNull($result);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Server\\ValueObject\\ControllerResult', $result);
        $this->assertSame(true, $result->getData());
    }

    /**
     * @test
     */
    public function processRequestWithLongerActionNameTest()
    {
        /** @var ControllerInterface $controller */
        $controller = $this->getMockBuilder('Cundd\\PersistentObjectStore\\Server\\Controller\\AbstractController')
            ->setMethods(array('getHelloWorldAction'))
            ->getMock();
        $controller
            ->expects($this->any())
            ->method('getHelloWorldAction')
            ->will($this->returnValue(true));

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(
            new Request('GET', '/_cundd-test-application/hello_world')
        );
        $response = new Response(new React_ConnectionStub());

        /** @var \Cundd\PersistentObjectStore\Server\ValueObject\ControllerResult $result */
        $result = $controller->processRequest($requestInfo, $response);
        $this->assertNotNull($result);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Server\\ValueObject\\ControllerResult', $result);
        $this->assertSame(true, $result->getData());
    }

    /**
     * @test
     */
    public function processRequestWithMultipleArgumentsTest()
    {
        /** @var ControllerInterface $controller */
        $controller = $this->getMockBuilder('Cundd\\PersistentObjectStore\\Server\\Controller\\AbstractController')
            ->setMethods(array('getHelloWorldAction'))
            ->getMock();
        $controller
            ->expects($this->any())
            ->method('getHelloWorldAction')
            ->will($this->returnValue(true));

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(
            new Request('GET', '/_cundd-test-application/hello_world/another_argument')
        );
        $response = new Response(new React_ConnectionStub());

        /** @var \Cundd\PersistentObjectStore\Server\ValueObject\ControllerResult $result */
        $result = $controller->processRequest($requestInfo, $response);
        $this->assertNotNull($result);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Server\\ValueObject\\ControllerResult', $result);
        $this->assertSame(true, $result->getData());
    }

    /**
     * @test
     * @expectedException \Cundd\PersistentObjectStore\Server\Exception\RequestMethodNotImplementedException
     */
    public function processRequestNotImplementedMethodTest()
    {
        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(
            new Request('GET', '/_cundd-test-application/my_method')
        );
        $response = new Response(new React_ConnectionStub());
        $this->fixture->processRequest($requestInfo, $response);
    }
}
