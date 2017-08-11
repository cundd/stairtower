<?php
declare(strict_types=1);

namespace Server\Controller;

use Cundd\Stairtower\Server\Controller\AbstractController;
use Cundd\Stairtower\Server\Controller\ControllerInterface;
use Cundd\Stairtower\Server\ValueObject\ControllerResult;
use Cundd\Stairtower\Server\ValueObject\RequestInfoFactory;
use Cundd\Stairtower\Tests\Fixtures\TestApplicationController;
use Cundd\Stairtower\Tests\Unit\AbstractCase;
use Cundd\Stairtower\Tests\Unit\RequestBuilderTrait;
use React\Http\Response;

/**
 * Tests for the abstract Controller implementation
 */
class AbstractControllerTest extends AbstractCase
{
    use RequestBuilderTrait;
    /**
     * @var ControllerInterface
     */
    protected $fixture;

    /**
     * @var RequestInfoFactory
     */
    protected $requestInfoFactory;

    protected function setUp()
    {
        parent::setUp();

        $this->makeClassAliasIfNotExists(
            TestApplicationController::class,
            'Cundd\\Test\\Controller\\ApplicationController'
        );
        $this->makeClassAliasIfNotExists(
            TestApplicationController::class,
            'Cundd\\TestModule\\Controller\\ApplicationController'
        );

        $this->fixture = $this->getMockForAbstractClass(
            AbstractController::class
        );

        $this->requestInfoFactory = $this->getDiContainer()->get(
            RequestInfoFactory::class
        );
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest('GET', '/_cundd-test-application/my_method')
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
     * @doesNotPerformAssertions
     */
    public function initializeTest()
    {
        $this->fixture->initialize();
    }


    /**
     * @test
     */
    public function getRequestTest()
    {
        $this->assertInstanceOf(
            \Cundd\Stairtower\Server\ValueObject\Request::class,
            $this->fixture->getRequest()
        );
    }

    /**
     * @test
     */
    public function setRequestTest()
    {
        $request = $this->buildRequest('GET', '/loaned/');
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest($request);
        $this->fixture->setRequest($requestInfo);
        $this->assertSame($requestInfo, $this->fixture->getRequest());
    }


    /**
     * @test
     */
    public function unsetRequestTest()
    {
        $request = $this->buildRequest('GET', '/loaned/');
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest($request);
        $this->fixture->setRequest($requestInfo);

        $this->fixture->unsetRequest();
        $this->assertNull($this->fixture->getRequest());
    }

    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function willInvokeActionTest()
    {
        $this->fixture->willInvokeAction('test');
    }

    /**
     * @test
     * @doesNotPerformAssertions
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
        /** @var ControllerInterface|\PHPUnit_Framework_MockObject_MockObject $controller */
        $controller = $this->getMockBuilder(AbstractController::class)
            ->setMethods(['getHelloAction'])
            ->getMock();
        $controller
            ->expects($this->any())
            ->method('getHelloAction')
            ->will($this->returnValue(true));

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest('GET', '/_cundd-test-application/hello')
        );

        /** @var \Cundd\Stairtower\Server\ValueObject\ControllerResult $result */
        $result = $controller->processRequest($requestInfo);
        $this->assertNotNull($result);
        $this->assertInstanceOf(ControllerResult::class, $result);
        $this->assertSame(true, $result->getData());
    }

    /**
     * @test
     */
    public function processRequestWithLongerActionNameTest()
    {
        /** @var ControllerInterface|\PHPUnit_Framework_MockObject_MockObject $controller */
        $controller = $this->getMockBuilder(AbstractController::class)
            ->setMethods(['getHelloWorldAction'])
            ->getMock();
        $controller
            ->expects($this->any())
            ->method('getHelloWorldAction')
            ->will($this->returnValue(true));

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest('GET', '/_cundd-test-application/hello_world')
        );

        /** @var \Cundd\Stairtower\Server\ValueObject\ControllerResult $result */
        $result = $controller->processRequest($requestInfo);
        $this->assertNotNull($result);
        $this->assertInstanceOf(ControllerResult::class, $result);
        $this->assertSame(true, $result->getData());
    }

    /**
     * @test
     */
    public function processRequestWithMultipleArgumentsTest()
    {
        /** @var ControllerInterface|\PHPUnit_Framework_MockObject_MockObject $controller */
        $controller = $this->getMockBuilder(AbstractController::class)
            ->setMethods(['getHelloWorldAction'])
            ->getMock();
        $controller
            ->expects($this->any())
            ->method('getHelloWorldAction')
            ->will($this->returnValue(true));

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest('GET', '/_cundd-test-application/hello_world/another_argument')
        );

        /** @var \Cundd\Stairtower\Server\ValueObject\ControllerResult $result */
        $result = $controller->processRequest($requestInfo);
        $this->assertNotNull($result);
        $this->assertInstanceOf(ControllerResult::class, $result);
        $this->assertSame(true, $result->getData());
    }

    /**
     * @test
     * @expectedException \Cundd\Stairtower\Server\Exception\RequestMethodNotImplementedException
     */
    public function processRequestNotImplementedMethodTest()
    {
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest('GET', '/_cundd-test-application/my_method')
        );
        $this->fixture->processRequest($requestInfo);
    }
}
