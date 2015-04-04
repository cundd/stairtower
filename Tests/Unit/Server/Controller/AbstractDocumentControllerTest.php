<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 08.01.15
 * Time: 11:08
 */

namespace Server\Controller;

use Cundd\PersistentObjectStore\AbstractDatabaseBasedCase;
use Cundd\PersistentObjectStore\Server\Controller\DocumentControllerInterface;
use Cundd\PersistentObjectStore\Server\ValueObject\RequestInfoFactory;
use React\Http\Request;

/**
 * Tests for the abstract Document Controller implementation
 *
 * @package Server\Controller
 */
class AbstractDocumentControllerTest extends AbstractDatabaseBasedCase
{
    /**
     * @var DocumentControllerInterface
     */
    protected $fixture;

    /**
     * @var RequestInfoFactory
     */
    protected $requestInfoFactory;

    protected function setUp()
    {
        parent::setUp();

        ///** @var CoordinatorInterface $coordinatorStub */
        $coordinatorStub = $this->getMockBuilder('Cundd\\PersistentObjectStore\\DataAccess\\Coordinator')->getMock();
        $coordinatorStub
            ->expects($this->any())
            ->method('getDatabase')
            ->will($this->returnValue($this->getSmallPeopleDatabase()));

        $coordinatorStub
            ->expects($this->any())
            ->method('databaseExists')
            ->will($this->returnValue(true));

        $this->fixture = $this->getMockBuilder('Cundd\\PersistentObjectStore\\Server\\Controller\\AbstractDocumentController')
            ->setMethods(array('getCoordinator'))
            ->getMock();
        $this->fixture
            ->expects($this->any())
            ->method('getCoordinator')
            ->will($this->returnValue($coordinatorStub));

        $this->requestInfoFactory = $this->getDiContainer()->get('Cundd\\PersistentObjectStore\\Server\\ValueObject\\RequestInfoFactory');
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new Request('GET', '/people-small/elliottgentry@andershun.com')
        );
        $this->fixture->setRequest($requestInfo);
    }

    protected function tearDown()
    {
        unset($this->fixture);
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getDatabaseForCurrentRequestTest()
    {
        $database = $this->fixture->getDatabaseForCurrentRequest();
        $this->assertNotNull($database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Domain\\Model\\DatabaseInterface', $database);
        $this->assertEquals('people-small', $database->getIdentifier());
    }

    /**
     * @test
     */
    public function getDatabaseForRequestInfoTest()
    {
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new Request('GET', '/people-small/elliottgentry@andershun.com')
        );
        $database    = $this->fixture->getDatabaseForRequest($requestInfo);
        $this->assertNotNull($database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Domain\\Model\\DatabaseInterface', $database);
        $this->assertEquals('people-small', $database->getIdentifier());
    }

    /**
     * @test
     */
    public function getDocumentForCurrentRequestTest()
    {
        $document = $this->fixture->getDocumentForCurrentRequest();
        $this->assertNotNull($document);
        $this->assertEquals('elliottgentry@andershun.com', $document->valueForKey('email'));
    }

    /**
     * @test
     */
    public function getDocumentForRequestTest()
    {
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new Request('GET', '/people-small/elliottgentry@andershun.com')
        );
        $document    = $this->fixture->getDocumentForRequest($requestInfo);
        $this->assertNotNull($document);
        $this->assertEquals('elliottgentry@andershun.com', $document->valueForKey('email'));
    }
}
