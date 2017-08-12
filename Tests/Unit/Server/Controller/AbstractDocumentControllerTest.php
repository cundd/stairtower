<?php
declare(strict_types=1);

namespace Server\Controller;

use Cundd\Stairtower\DataAccess\CoordinatorInterface;
use Cundd\Stairtower\Domain\Model\DatabaseInterface;
use Cundd\Stairtower\Server\Controller\AbstractDocumentController;
use Cundd\Stairtower\Server\Controller\DocumentControllerInterface;
use Cundd\Stairtower\Server\ValueObject\RequestInfoFactory;
use Cundd\Stairtower\Tests\Unit\AbstractDatabaseBasedCase;
use Cundd\Stairtower\Tests\Unit\RequestBuilderTrait;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Tests for the abstract Document Controller implementation
 */
class AbstractDocumentControllerTest extends AbstractDatabaseBasedCase
{
    use RequestBuilderTrait;

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

        /** @var CoordinatorInterface|ObjectProphecy $coordinatorStub */
        $coordinatorStub = $this->prophesize(CoordinatorInterface::class);
        /** @var string $stringArgument */
        $stringArgument = Argument::type('string');
        $coordinatorStub->getDatabase($stringArgument)->willReturn($this->getSmallPeopleDatabase());

        $coordinatorStub->databaseExists($stringArgument)->willReturn(true);

        $this->fixture = $this->getMockBuilder(AbstractDocumentController::class)
            ->setMethods(['getCoordinator'])
            ->getMock();
        $this->fixture
            ->expects($this->any())
            ->method('getCoordinator')
            ->will($this->returnValue($coordinatorStub->reveal()));

        $this->requestInfoFactory = $this->getDiContainer()->get(RequestInfoFactory::class);
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest('GET', '/people-small/elliottgentry@andershun.com')
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
        $this->assertInstanceOf(DatabaseInterface::class, $database);
        $this->assertEquals('people-small', $database->getIdentifier());
    }

    /**
     * @test
     */
    public function getDatabaseForRequestInfoTest()
    {
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest('GET', '/people-small/elliottgentry@andershun.com')
        );
        $database = $this->fixture->getDatabaseForRequest($requestInfo);
        $this->assertNotNull($database);
        $this->assertInstanceOf(DatabaseInterface::class, $database);
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
            $this->buildRequest('GET', '/people-small/elliottgentry@andershun.com')
        );
        $document = $this->fixture->getDocumentForRequest($requestInfo);
        $this->assertNotNull($document);
        $this->assertEquals('elliottgentry@andershun.com', $document->valueForKey('email'));
    }
}
