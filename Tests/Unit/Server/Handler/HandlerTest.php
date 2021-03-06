<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Tests\Unit\Server\Handler;


use Cundd\Stairtower\Configuration\ConfigurationManager;
use Cundd\Stairtower\Constants;
use Cundd\Stairtower\Domain\Model\DatabaseInterface;
use Cundd\Stairtower\Domain\Model\Document;
use Cundd\Stairtower\Domain\Model\DocumentInterface;
use Cundd\Stairtower\Filter\FilterResultInterface;
use Cundd\Stairtower\Memory\Manager;
use Cundd\Stairtower\Server\Handler\HandlerInterface;
use Cundd\Stairtower\Server\Handler\HandlerResultInterface;
use Cundd\Stairtower\Server\ValueObject\RequestInfoFactory;
use Cundd\Stairtower\Tests\Unit\AbstractDatabaseBasedCase;
use Cundd\Stairtower\Tests\Unit\RequestBuilderTrait;

/**
 * Handler test
 */
class HandlerTest extends AbstractDatabaseBasedCase
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
     * @var RequestInfoFactory
     */
    protected $requestInfoFactory;

    /**
     * @var string
     */
    protected $publicResourcesPath = '';

    /**
     * @test
     */
    public function noRouteTest()
    {
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            RequestBuilderTrait::buildRequest('GET', '/')
        );
        $handlerResult = $this->fixture->noRoute($requestInfo);
        $this->assertInstanceOf(
            HandlerResultInterface::class,
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertEquals(Constants::MESSAGE_JSON_WELCOME, $handlerResult->getData());
    }

    /**
     * @test
     */
    public function createTest()
    {
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            RequestBuilderTrait::buildRequest('POST', '/contacts/')
        );
        $data = ['email' => 'info-for-me@cundd.net', 'name' => 'Daniel'];
        $handlerResult = $this->fixture->create($requestInfo, $data);

        $this->assertInstanceOf(
            HandlerResultInterface::class,
            $handlerResult
        );
        $this->assertEquals(201, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());
        $this->assertInstanceOf(
            DocumentInterface::class,
            $handlerResult->getData()
        );

        /** @var DocumentInterface $dataInstance */
        $dataInstance = $handlerResult->getData();
        $this->assertEquals('info-for-me@cundd.net', $dataInstance->valueForKey('email'));

        $this->assertTrue($this->database->contains($dataInstance));

        $i = 0;
        do {
            $data = ['email' => "info$i-for-me@cundd.net", 'name' => 'Daniel'];
            $handlerResult = $this->fixture->create($requestInfo, $data);
        } while (++$i < 10000);
        // Validate the last result
        $this->assertInstanceOf(
            HandlerResultInterface::class,
            $handlerResult
        );
        $this->assertEquals(201, $handlerResult->getStatusCode());
        $dataInstance = $handlerResult->getData();
        $this->assertEquals(sprintf('info%d-for-me@cundd.net', $i - 1), $dataInstance->valueForKey('email'));


        $iHalf = intval($i / 2);
        $this->assertTrue($this->database->contains("info$iHalf-for-me@cundd.net"));
    }

    /**
     * @test
     */
    public function createDatabaseTest()
    {
        $databaseIdentifier = 'test-db-' . time();
        $expectedPath = ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath(
                'writeDataPath'
            ) . $databaseIdentifier . '.json';

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            RequestBuilderTrait::buildRequest('PUT', sprintf('/%s/', $databaseIdentifier))
        );
        $databaseOptions = [];
        // Todo: Enable Database creation parameters
        // $databaseOptions = array('type' => 'memory');
        $handlerResult = $this->fixture->create($requestInfo, $databaseOptions);

        $this->assertInstanceOf(
            HandlerResultInterface::class,
            $handlerResult
        );
        $this->assertEquals(201, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());

        $this->assertFileExists($expectedPath);
        unlink($expectedPath);
    }

    /**
     * @test
     * @expectedException \Cundd\Stairtower\Server\Exception\InvalidRequestParameterException
     */
    public function createWithDataIdentifierShouldFailTest()
    {
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            RequestBuilderTrait::buildRequest('POST', '/contacts/info@cundd.net')
        );
        $data = ['email' => 'info-for-me@cundd.net', 'name' => 'Daniel'];
        $handlerResult = $this->fixture->create($requestInfo, $data);

        $this->assertInstanceOf(
            HandlerResultInterface::class,
            $handlerResult
        );
        $this->assertEquals(201, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());
        $this->assertInstanceOf(
            DocumentInterface::class,
            $handlerResult->getData()
        );

        /** @var DocumentInterface $dataInstance */
        $dataInstance = $handlerResult->getData();
        $this->assertEquals('info-for-me@cundd.net', $dataInstance->valueForKey('email'));

    }

    /**
     * @test
     */
    public function readTest()
    {
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            RequestBuilderTrait::buildRequest(
                'GET',
                '/contacts/info@cundd.net'
            )
        );
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            HandlerResultInterface::class,
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());
        $this->assertInstanceOf(
            DocumentInterface::class,
            $handlerResult->getData()
        );

        /** @var DocumentInterface $dataInstance */
        $dataInstance = $handlerResult->getData();
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKey('email'));
    }

    /**
     * @test
     */
    public function readDatabaseTest()
    {
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            RequestBuilderTrait::buildRequest('GET', '/contacts/')
        );
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            HandlerResultInterface::class,
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());
        $this->assertInstanceOf(
            DatabaseInterface::class,
            $handlerResult->getData()
        );

        /** @var DocumentInterface $dataInstance */
        $dataInstance = $handlerResult->getData()->current();
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKey('email'));
    }

    /**
     * @test
     */
    public function readWithSearchTest()
    {
        parse_str('firstName=Daniel', $query);
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            RequestBuilderTrait::buildRequest('GET', '/contacts/', $query)
        );
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            HandlerResultInterface::class,
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());
        $this->assertInstanceOf(
            FilterResultInterface::class,
            $handlerResult->getData()
        );
        $this->assertEquals(1, $handlerResult->getData()->count());

        /** @var DocumentInterface $dataInstance */
        $dataInstance = $handlerResult->getData()->current();
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKey('email'));
    }

    /**
     * @test
     */
    public function readWithEmptyResultSearchTest()
    {
        parse_str('firstName=Some-thing-not-existing', $query);
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            RequestBuilderTrait::buildRequest('GET', '/contacts/', $query)
        );
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            HandlerResultInterface::class,
            $handlerResult
        );
        $this->assertEquals(404, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());
        $this->assertInstanceOf(
            FilterResultInterface::class,
            $handlerResult->getData()
        );
        $this->assertEquals(0, $handlerResult->getData()->count());


        parse_str('some-thing-not-existing=Daniel', $query);
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            RequestBuilderTrait::buildRequest('GET', '/contacts/', $query)
        );
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            HandlerResultInterface::class,
            $handlerResult
        );
        $this->assertEquals(404, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());
        $this->assertInstanceOf(
            FilterResultInterface::class,
            $handlerResult->getData()
        );
        $this->assertEquals(0, $handlerResult->getData()->count());
    }

    /**
     * @test
     */
    public function updateTest()
    {
        $newName = 'Steve ' . time();
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            RequestBuilderTrait::buildRequest('PUT', '/contacts/info@cundd.net')
        );
        $data = ['email' => 'info@cundd.net', 'name' => $newName];

        $handlerResult = $this->fixture->update($requestInfo, $data);
        $this->assertInstanceOf(
            HandlerResultInterface::class,
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());
        $this->assertInstanceOf(
            DocumentInterface::class,
            $handlerResult->getData()
        );

        /** @var DocumentInterface $dataInstance */
        $dataInstance = $handlerResult->getData();
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKey('email'));
        $this->assertEquals($newName, $dataInstance->valueForKey('name'));


        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            RequestBuilderTrait::buildRequest('PUT', '/contacts/email@does-not-exist.net')
        );
        $data = [
            'what ever' => 'this will not be updated',
            'email'     => 'info@cundd.net',
            'name'      => 'Daniel',
        ];

        $handlerResult = $this->fixture->update($requestInfo, $data);
        $this->assertInstanceOf(
            HandlerResultInterface::class,
            $handlerResult
        );
        $this->assertEquals(404, $handlerResult->getStatusCode());

    }

    /**
     * @test
     */
    public function deleteTest()
    {
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            RequestBuilderTrait::buildRequest('DELETE', '/contacts/info@cundd.net')
        );
        $handlerResult = $this->fixture->delete($requestInfo);

        $this->assertInstanceOf(
            HandlerResultInterface::class,
            $handlerResult
        );
        $this->assertEquals(202, $handlerResult->getStatusCode());
        $this->assertEquals('Document "info@cundd.net" deleted', $handlerResult->getData());

        /** @var DocumentInterface $dataInstance */
        $dataInstance = new Document(['email' => 'info@cundd.net']);

        $this->assertFalse($this->database->contains($dataInstance));
    }

    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function deleteDatabaseTest()
    {
        // Running this test would remove our test data :(
        /*
        $requestInfo   = $this->requestInfoFactory->buildRequestFromRawRequestRequestBuilderTrait::buildRequest('DELETE', '/contacts/'));
        $handlerResult = $this->fixture->delete($requestInfo);

        $this->assertInstanceOf(HandlerResultInterface::class,
            $handlerResult);
        $this->assertEquals(204, $handlerResult->getStatusCode());
        $this->assertEquals('Database "contacts" deleted', $handlerResult->getData());
        */
    }

    /**
     * @test
     */
    public function getStatsActionTest()
    {
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            RequestBuilderTrait::buildRequest('GET', '/_stats/')
        );
        $handlerResult = $this->fixture->getStatsAction($requestInfo);
        $this->assertInstanceOf(
            HandlerResultInterface::class,
            $handlerResult
        );
    }

    /**
     * @test
     */
    public function getAssetActionTest()
    {
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            RequestBuilderTrait::buildRequest('GET', '/_asset/book.json')
        );
        $handlerResult = $this->fixture->getAssetAction($requestInfo);
        $this->assertInstanceOf(
            HandlerResultInterface::class,
            $handlerResult
        );
        $this->assertSame(200, $handlerResult->getStatusCode());
        $this->assertContains('Beltz', $handlerResult->getData());

    }

    /**
     * @test
     */
    public function doNotGetAssetActionTest()
    {
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            RequestBuilderTrait::buildRequest('GET', '/_asset/not-existing.jpg')
        );
        $handlerResult = $this->fixture->getAssetAction($requestInfo);
        $this->assertInstanceOf(
            HandlerResultInterface::class,
            $handlerResult
        );
        $this->assertSame(404, $handlerResult->getStatusCode());
    }

    protected function setUp()
    {
        Manager::freeAll();

        $configurationManager = ConfigurationManager::getSharedInstance();
        $this->publicResourcesPath = $configurationManager->getConfigurationForKeyPath('publicResources');
        $configurationManager->setConfigurationForKeyPath('publicResources', __DIR__ . '/../../../Resources/');

        $this->requestInfoFactory = $this->getDiContainer()->get(RequestInfoFactory::class);
        parent::setUp();
        $this->database = $this->getCoordinator()->getDatabase('contacts');
    }

    protected function tearDown()
    {
        Manager::freeAll();
        ConfigurationManager::getSharedInstance()->setConfigurationForKeyPath(
            'publicResources',
            $this->publicResourcesPath
        );
    }
}
