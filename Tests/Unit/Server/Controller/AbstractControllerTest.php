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
        $this->fixture->setRequestInfo($requestInfo);
    }

    protected function tearDown()
    {
        $this->fixture->unsetRequestInfo();
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
            'Cundd\\PersistentObjectStore\\Server\\ValueObject\\RequestInfo',
            $this->fixture->getRequestInfo());
    }

    /**
     * @test
     */
    public function setRequestInfoTest()
    {
        $request     = new Request('GET', '/loaned/');
        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest($request);
        $this->fixture->setRequestInfo($requestInfo);
        $this->assertSame($requestInfo, $this->fixture->getRequestInfo());
        $this->assertSame($request, $this->fixture->getRequest());
    }


    /**
     * @test
     */
    public function unsetRequestInfoTest()
    {
        $request     = new Request('GET', '/loaned/');
        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest($request);
        $this->fixture->setRequestInfo($requestInfo);

        $this->fixture->unsetRequestInfo();
        $this->assertNull($this->fixture->getRequestInfo());
        $this->assertNull($this->fixture->getRequest());
    }

    /**
     * @test
     */
    public function getRequestTest()
    {
        $this->assertInstanceOf('React\\Http\\Request', $this->fixture->getRequest());
        $this->assertEquals('/_cundd-test-application/my_method', $this->fixture->getRequest()->getPath());
        $this->assertSame($this->fixture->getRequest(), $this->fixture->getRequestInfo()->getRequest());
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
}
