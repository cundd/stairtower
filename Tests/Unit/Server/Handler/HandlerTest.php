<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 11.10.14
 * Time: 20:18
 */

namespace Cundd\PersistentObjectStore\Server\Handler;


use Cundd\PersistentObjectStore\AbstractCase;
use Cundd\PersistentObjectStore\Configuration\ConfigurationManager;
use Cundd\PersistentObjectStore\Constants;
use Cundd\PersistentObjectStore\Domain\Model\DatabaseInterface;
use Cundd\PersistentObjectStore\Domain\Model\Document;
use Cundd\PersistentObjectStore\Domain\Model\DocumentInterface;
use Cundd\PersistentObjectStore\Memory\Manager;
use Cundd\PersistentObjectStore\Server\ValueObject\RequestInfoFactory;
use React\Http\Request;

/**
 * Handler test
 *
 * @package Cundd\PersistentObjectStore\Server\Handler
 */
class HandlerTest extends AbstractCase
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
    public function noRouteTest()
    {
        $requestInfo   = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET', '/'));
        $handlerResult = $this->fixture->noRoute($requestInfo);
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface',
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
        $requestInfo   = RequestInfoFactory::buildRequestInfoFromRequest(new Request('POST', '/contacts/'));
        $data          = array('email' => 'info-for-me@cundd.net', 'name' => 'Daniel');
        $handlerResult = $this->fixture->create($requestInfo, $data);

        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface',
            $handlerResult
        );
        $this->assertEquals(201, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Domain\\Model\\DocumentInterface',
            $handlerResult->getData()
        );

        /** @var DocumentInterface $dataInstance */
        $dataInstance = $handlerResult->getData();
        $this->assertEquals('info-for-me@cundd.net', $dataInstance->valueForKey('email'));

        $this->assertTrue($this->database->contains($dataInstance));

        $i = 0;
        do {
            $data          = array('email' => "info$i-for-me@cundd.net", 'name' => 'Daniel');
            $handlerResult = $this->fixture->create($requestInfo, $data);
        } while (++$i < 10000);
        // Validate the last result
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface',
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
        $expectedPath       = ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('writeDataPath') . $databaseIdentifier . '.json';

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(
            new Request('PUT', sprintf('/%s/', $databaseIdentifier))
        );
        $databaseOptions = array('type' => 'memory');
        $handlerResult   = $this->fixture->create($requestInfo, $databaseOptions);

        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface',
            $handlerResult
        );
        $this->assertEquals(201, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());

        $this->assertFileExists($expectedPath);
        unlink($expectedPath);
    }

    /**
     * @test
     * @expectedException \Cundd\PersistentObjectStore\Server\Exception\InvalidRequestParameterException
     */
    public function createWithDataIdentifierShouldFailTest()
    {
        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(
            new Request('POST', '/contacts/info@cundd.net')
        );
        $data          = array('email' => 'info-for-me@cundd.net', 'name' => 'Daniel');
        $handlerResult = $this->fixture->create($requestInfo, $data);

        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface',
            $handlerResult
        );
        $this->assertEquals(201, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Domain\\Model\\DocumentInterface',
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
        $requestInfo   = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET',
            '/contacts/info@cundd.net'));
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface',
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Domain\\Model\\DocumentInterface',
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
        $requestInfo   = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET', '/contacts/'));
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface',
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Domain\\Model\\DatabaseInterface',
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
        $requestInfo   = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET', '/contacts/', $query));
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface',
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Filter\\FilterResultInterface',
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
    public function readWithExpandTest()
    {
        // Query '$expand=person-contacts-email'
        $queryString = vsprintf('%s=person%scontacts%semail', [
            Constants::REQUEST_EXPAND_KEY,
            Constants::REQUEST_EXPAND_SPLIT_CHAR,
            Constants::REQUEST_EXPAND_SPLIT_CHAR,
        ]);
        parse_str($queryString, $query);
        $requestInfo   = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET', '/loaned/', $query));
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface',
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());

        $this->assertInstanceOf('SplFixedArray', $handlerResult->getData());
        $this->assertEquals(4, $handlerResult->getData()->count());

        /** @var DocumentInterface $dataInstance */
        $dataInstance = $handlerResult->getData()->current();
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person.email'));
        $handlerResult->getData()->next();
        $handlerResult->getData()->next();
        $dataInstance = $handlerResult->getData()->current();
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person.email'));
    }

    /**
     * @test
     */
    public function readWithMoreThanOneExpandsTest()
    {
        $queryString = vsprintf('%s=person%scontacts%semail%sbook%sbook%sisbn_10', [
            Constants::REQUEST_EXPAND_KEY,
            Constants::REQUEST_EXPAND_SPLIT_CHAR,
            Constants::REQUEST_EXPAND_SPLIT_CHAR,
            Constants::REQUEST_EXPAND_DELIMITER,
            Constants::REQUEST_EXPAND_SPLIT_CHAR,
            Constants::REQUEST_EXPAND_SPLIT_CHAR,
        ]);
        parse_str($queryString, $query);
        $requestInfo   = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET', '/loaned/', $query));
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface',
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());

        $this->assertInstanceOf('SplFixedArray', $handlerResult->getData());
        $this->assertEquals(4, $handlerResult->getData()->count());

        /** @var DocumentInterface $dataInstance */
        $dataInstance = $handlerResult->getData()->current();
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person.email'));
        $this->assertEquals('0395595118', $dataInstance->valueForKeyPath('book.isbn_10'));
        $this->assertEquals('The Lord of the rings', $dataInstance->valueForKeyPath('book.title'));
        $handlerResult->getData()->next();
        $handlerResult->getData()->next();
        $dataInstance = $handlerResult->getData()->current();
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person.email'));
        $this->assertEquals('0345253426', $dataInstance->valueForKeyPath('book.isbn_10'));
        $this->assertEquals('The Hobbit', $dataInstance->valueForKeyPath('book.title'));
    }

    /**
     * @test
     */
    public function readWithSearchAndExpandTest()
    {
        // Query 'title=The Hobbit&$expand=person-contacts-email'
        $queryString = vsprintf('title=The Hobbit&%s=person%scontacts%semail', [
            Constants::REQUEST_EXPAND_KEY,
            Constants::REQUEST_EXPAND_SPLIT_CHAR,
            Constants::REQUEST_EXPAND_SPLIT_CHAR,
        ]);
        parse_str($queryString, $query);
        $requestInfo   = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET', '/loaned/', $query));
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface',
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());

        $this->assertInstanceOf('SplFixedArray', $handlerResult->getData());
        $this->assertEquals(1, $handlerResult->getData()->count());

        /** @var DocumentInterface $dataInstance */
        $dataInstance = $handlerResult->getData()->current();
        $this->assertEquals('The Hobbit', $dataInstance->valueForKeyPath('title'));
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person.email'));


        // Query '$expand=person-contacts-email&title=The Hobbit'
        $queryString = vsprintf('%s=person%scontacts%semail&title=The Hobbit', [
            Constants::REQUEST_EXPAND_KEY,
            Constants::REQUEST_EXPAND_SPLIT_CHAR,
            Constants::REQUEST_EXPAND_SPLIT_CHAR,
        ]);
        parse_str($queryString, $query);
        $requestInfo   = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET', '/loaned/', $query));
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface',
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());

        $this->assertInstanceOf('SplFixedArray', $handlerResult->getData());
        $this->assertEquals(1, $handlerResult->getData()->count());

        /** @var DocumentInterface $dataInstance */
        $dataInstance = $handlerResult->getData()->current();
        $this->assertEquals('The Hobbit', $dataInstance->valueForKeyPath('title'));
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person.email'));
    }

    /**
     * @test
     */
    public function updateTest()
    {
        $newName     = 'Steve ' . time();
        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('PUT', '/contacts/info@cundd.net'));
        $data        = array('email' => 'info@cundd.net', 'name' => $newName);

        $handlerResult = $this->fixture->update($requestInfo, $data);
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface',
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Domain\\Model\\DocumentInterface',
            $handlerResult->getData()
        );

        /** @var DocumentInterface $dataInstance */
        $dataInstance = $handlerResult->getData();
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKey('email'));
        $this->assertEquals($newName, $dataInstance->valueForKey('name'));


        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(
            new Request('PUT', '/contacts/email@does-not-exist.net')
        );
        $data        = array(
            'what ever' => 'this will not be updated',
            'email'     => 'info@cundd.net',
            'name'      => 'Daniel'
        );

        $handlerResult = $this->fixture->update($requestInfo, $data);
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface',
            $handlerResult
        );
        $this->assertEquals(404, $handlerResult->getStatusCode());

    }

    /**
     * @test
     */
    public function deleteTest()
    {
        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(
            new Request('DELETE', '/contacts/info@cundd.net')
        );
        $handlerResult = $this->fixture->delete($requestInfo);

        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface',
            $handlerResult
        );
        $this->assertEquals(204, $handlerResult->getStatusCode());
        $this->assertEquals('Document "info@cundd.net" deleted', $handlerResult->getData());

        /** @var DocumentInterface $dataInstance */
        $dataInstance = new Document(['email' => 'info@cundd.net']);

        $this->assertFalse($this->database->contains($dataInstance));
    }

    /**
     * @test
     */
    public function deleteDatabaseTest()
    {
        // Running this test would remove our test data :(
        /*
        $requestInfo   = RequestInfoFactory::buildRequestInfoFromRequest(new Request('DELETE', '/contacts/'));
        $handlerResult = $this->fixture->delete($requestInfo);

        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface',
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
        $requestInfo   = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET', '/_stats/'));
        $handlerResult = $this->fixture->getStatsAction($requestInfo);
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface',
            $handlerResult
        );

    }

    protected function setUp()
    {
        Manager::freeAll();

        $diContainer = $this->getDiContainer();
        $server      = $diContainer->get('Cundd\\PersistentObjectStore\\Server\\DummyServer');
        $diContainer->set('Cundd\\PersistentObjectStore\\Server\\ServerInterface', $server);

        $coordinator = $diContainer->get('Cundd\\PersistentObjectStore\\DataAccess\\CoordinatorInterface');


        parent::setUp();
        $this->database = $coordinator->getDatabase('contacts');
    }

    protected function tearDown()
    {
        Manager::freeAll();
    }
}
