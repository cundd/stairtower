<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 11.10.14
 * Time: 20:18
 */

namespace Cundd\PersistentObjectStore\Server\Handler;


use Cundd\PersistentObjectStore\AbstractCase;
use Cundd\PersistentObjectStore\Domain\Model\DatabaseInterface;
use Cundd\PersistentObjectStore\Memory\Manager;
use Cundd\PersistentObjectStore\Meta\Database\Property\Description;
use Cundd\PersistentObjectStore\Server\ValueObject\RequestInfoFactory;
use React\Http\Request;

/**
 * Handler test for describe requests
 *
 * @package Cundd\PersistentObjectStore\Server\Handler
 */
class HandlerDescribeTest extends AbstractCase
{
    /**
     * @var HandlerInterface
     */
    protected $fixture;

    /**
     * @var DatabaseInterface
     */
    protected $database;

    /**
     * @test
     */
    public function describeTest()
    {
        $requestInfo   = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET', '/contacts/_describe'));
        $handlerResult = $this->fixture->getDescribeAction($requestInfo);
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface',
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());
        $this->assertInternalType('array', $handlerResult->getData());

        $resultData = $handlerResult->getData();

        /** @var Description $description */
        $description = current($resultData);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Meta\\Database\\Property\\Description', $description);
        $this->assertEquals('firstName', $description->getKey());
        $this->assertEquals(6, $description->getCount());
        $this->assertEquals(array(Description::TYPE_STRING), $description->getTypes());
    }

    /**
     * @test
     */
    public function describeIgnoresDataIdentifierTest()
    {
        $requestInfo   = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET',
            '/contacts/info/_describe'));
        $handlerResult = $this->fixture->getDescribeAction($requestInfo);
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface',
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());
        $this->assertInternalType('array', $handlerResult->getData());

        $resultData = $handlerResult->getData();

        /** @var Description $description */
        $description = current($resultData);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Meta\\Database\\Property\\Description', $description);
        $this->assertEquals('firstName', $description->getKey());
        $this->assertEquals(6, $description->getCount());
        $this->assertEquals(array(Description::TYPE_STRING), $description->getTypes());
    }

    /**
     * @test
     */
    public function canNotDescribeWithoutDatabaseTest()
    {
        $requestInfo   = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET', '/_describe'));
        $handlerResult = $this->fixture->getDescribeAction($requestInfo);
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface',
            $handlerResult
        );
        $this->assertEquals(400, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());
        $this->assertInternalType('string', $handlerResult->getData());
    }

    protected function setUp()
    {
        if (class_exists('Cundd\PersistentObjectStore\Memory\Manager')) {
            Manager::freeAll();
        }

        $diContainer = $this->getDiContainer();
        $server      = $diContainer->get('Cundd\\PersistentObjectStore\\Server\\DummyServer');
        $diContainer->set('Cundd\\PersistentObjectStore\\Server\\ServerInterface', $server);

        $this->setUpXhprof();

        $this->fixture = $this->getDiContainer()->get('Cundd\\PersistentObjectStore\\Server\\Handler\\Handler');
    }

    protected function tearDown()
    {
        Manager::freeAll();
    }
}
