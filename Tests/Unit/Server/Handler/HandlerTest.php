<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 11.10.14
 * Time: 20:18
 */

namespace Cundd\PersistentObjectStore\Server\Handler;


use Cundd\PersistentObjectStore\AbstractCase;
use Cundd\PersistentObjectStore\Constants;
use Cundd\PersistentObjectStore\Domain\Model\Data;
use Cundd\PersistentObjectStore\Domain\Model\DatabaseInterface;
use Cundd\PersistentObjectStore\Domain\Model\DataInterface;
use Cundd\PersistentObjectStore\Memory\Manager;
use Cundd\PersistentObjectStore\Server\ValueObject\RequestInfoFactory;
use React\Http\Request;

/**
 * Handler test
 *
 * @package Cundd\PersistentObjectStore\Server\Handler
 */
class HandlerTest extends AbstractCase {
	/**
	 * @var HandlerInterface
	 */
	protected $fixture;

	/**
	 * @var DatabaseInterface
	 */
	protected $database;

	protected function setUp() {
		Manager::freeAll();

		$diContainer = $this->getDiContainer();
		$server = $diContainer->get('Cundd\\PersistentObjectStore\\Server\\DummyServer');
		$diContainer->set('Cundd\\PersistentObjectStore\\Server\\ServerInterface', $server);

		$coordinator = $diContainer->get('Cundd\\PersistentObjectStore\\DataAccess\\CoordinatorInterface');


		parent::setUp();
		$this->database = $coordinator->getDatabase('contacts');
	}

	protected function tearDown() {
		Manager::freeAll();
	}


	/**
	 * @test
	 */
	public function noRouteTest() {
		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET', '/contacts/info@cundd.net'));
		$handlerResult = $this->fixture->noRoute($requestInfo);
		$this->assertInstanceOf('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface', $handlerResult);
		$this->assertEquals(200, $handlerResult->getStatusCode());
		$this->assertEquals(Constants::MESSAGE_JSON_WELCOME, $handlerResult->getData());
	}

	/**
	 * @test
	 */
	public function createTest() {
		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('POST', '/contacts/'));
		$data = array('email' => 'info-for-me@cundd.net', 'name' => 'Daniel');
		$handlerResult = $this->fixture->create($requestInfo, $data);

		$this->assertInstanceOf('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface', $handlerResult);
		$this->assertEquals(201, $handlerResult->getStatusCode());
		$this->assertNotNull($handlerResult->getData());
		$this->assertInstanceOf('Cundd\\PersistentObjectStore\\Domain\\Model\\DataInterface', $handlerResult->getData());

		/** @var DataInterface $dataInstance */
		$dataInstance = $handlerResult->getData();
		$this->assertEquals('info-for-me@cundd.net', $dataInstance->valueForKey('email'));

		$this->assertTrue($this->database->contains($dataInstance));
	}



	/**
	 * @test
	 * @expectedException \Cundd\PersistentObjectStore\Server\Exception\InvalidRequestParameterException
	 */
	public function createWithDataIdentifierShouldFailTest() {
		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('POST', '/contacts/info@cundd.net'));
		$data = array('email' => 'info-for-me@cundd.net', 'name' => 'Daniel');
		$handlerResult = $this->fixture->create($requestInfo, $data);

		$this->assertInstanceOf('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface', $handlerResult);
		$this->assertEquals(201, $handlerResult->getStatusCode());
		$this->assertNotNull($handlerResult->getData());
		$this->assertInstanceOf('Cundd\\PersistentObjectStore\\Domain\\Model\\DataInterface', $handlerResult->getData());

		/** @var DataInterface $dataInstance */
		$dataInstance = $handlerResult->getData();
		$this->assertEquals('info-for-me@cundd.net', $dataInstance->valueForKey('email'));

	}

	/**
	 * @test
	 */
	public function readTest() {
		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET', '/contacts/info@cundd.net'));
		$handlerResult = $this->fixture->read($requestInfo);
		$this->assertInstanceOf('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface', $handlerResult);
		$this->assertEquals(200, $handlerResult->getStatusCode());
		$this->assertNotNull($handlerResult->getData());
		$this->assertInstanceOf('Cundd\\PersistentObjectStore\\Domain\\Model\\DataInterface', $handlerResult->getData());

		/** @var DataInterface $dataInstance */
		$dataInstance = $handlerResult->getData();
		$this->assertEquals('info@cundd.net', $dataInstance->valueForKey('email'));
	}

	/**
	 * @test
	 */
	public function readDatabaseTest() {
		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET', '/contacts/'));
		$handlerResult = $this->fixture->read($requestInfo);
		$this->assertInstanceOf('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface', $handlerResult);
		$this->assertEquals(200, $handlerResult->getStatusCode());
		$this->assertNotNull($handlerResult->getData());
		$this->assertInstanceOf('Cundd\\PersistentObjectStore\\Domain\\Model\\DatabaseInterface', $handlerResult->getData());

		/** @var DataInterface $dataInstance */
		$dataInstance = $handlerResult->getData()->current();
		$this->assertEquals('info@cundd.net', $dataInstance->valueForKey('email'));
	}

	/**
	 * @test
	 */
	public function readWithSearchTest() {
		$query = array();
		parse_str('firstName=Daniel', $query);
		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET', '/contacts/', $query));
		$handlerResult = $this->fixture->read($requestInfo);
		$this->assertInstanceOf('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface', $handlerResult);
		$this->assertEquals(200, $handlerResult->getStatusCode());
		$this->assertNotNull($handlerResult->getData());
		$this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResultInterface', $handlerResult->getData());
		$this->assertEquals(1, $handlerResult->getData()->count());

		/** @var DataInterface $dataInstance */
		$dataInstance = $handlerResult->getData()->current();
		$this->assertEquals('info@cundd.net', $dataInstance->valueForKey('email'));
	}

	/**
	 * @test
	 */
	public function updateTest() {
		$newName = 'Steve ' . time();
		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('PUT', '/contacts/info@cundd.net'));
		$data = array('email' => 'info@cundd.net', 'name' => $newName);

		$handlerResult = $this->fixture->update($requestInfo, $data);
		$this->assertInstanceOf('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface', $handlerResult);
		$this->assertEquals(200, $handlerResult->getStatusCode());
		$this->assertNotNull($handlerResult->getData());
		$this->assertInstanceOf('Cundd\\PersistentObjectStore\\Domain\\Model\\DataInterface', $handlerResult->getData());

		/** @var DataInterface $dataInstance */
		$dataInstance = $handlerResult->getData();
		$this->assertEquals('info@cundd.net', $dataInstance->valueForKey('email'));
		$this->assertEquals($newName, $dataInstance->valueForKey('name'));


		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('PUT', '/contacts/email@does-not-exist.net'));
		$data = array('what ever' => 'this will not be updated', 'email' => 'info@cundd.net', 'name' => 'Daniel');

		$handlerResult = $this->fixture->update($requestInfo, $data);
		$this->assertInstanceOf('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface', $handlerResult);
		$this->assertEquals(404, $handlerResult->getStatusCode());

	}

	/**
	 * @test
	 */
	public function deleteTest() {
		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('DELETE', '/contacts/info@cundd.net'));
		$handlerResult = $this->fixture->delete($requestInfo);

		$this->assertInstanceOf('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface', $handlerResult);
		$this->assertEquals(204, $handlerResult->getStatusCode());
		$this->assertNull($handlerResult->getData());

		/** @var DataInterface $dataInstance */
		$dataInstance = new Data(['email' => 'info@cundd.net']);

		$this->assertFalse($this->database->contains($dataInstance));
	}

	/**
	 * @test
	 */
	public function getStatsActionTest() {
		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET', '/_stats/'));
		$handlerResult = $this->fixture->getStatsAction($requestInfo);
		$this->assertInstanceOf('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface', $handlerResult);

	}
}
 