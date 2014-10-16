<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 13.10.14
 * Time: 17:17
 */

namespace Cundd\PersistentObjectStore\Server\BodyParser;


use Cundd\PersistentObjectStore\AbstractCase;
use React\Http\Request;

class JsonBodyParserTest_DummyRequestClass {
	public function getPath() {
		return '/contacts/';
	}

	public function getMethod() {
		return 'GET';
	}

}

/**
 * JSON based body parser
 *
 * @package Cundd\PersistentObjectStore\Server\BodyParser
 */
class JsonBodyParserTest extends AbstractCase {
	/**
	 * @var BodyParserInterface
	 */
	protected $fixture;
	/**
	 * @test
	 */
	public function parseBodyTest() {

		/** @var Request $dummyRequest */
		$dummyRequest = new JsonBodyParserTest_DummyRequestClass();
		$this->assertArrayHasKey('email', $this->fixture->parse('{"email":"info@cundd.net"}', $dummyRequest));
		$this->assertArrayHasKey('email', $this->fixture->parse('{"email":"info@cundd.net","name":"Daniel"}', $dummyRequest));
		$this->assertArrayHasKey('email', $this->fixture->parse('{"name":"Daniel","email":"info@cundd.net"}', $dummyRequest));


		$testContent = array(
			array(
				'email' => 'info@cundd.net',
				'name' => 'Daniel',
			),
			array(
				'email' => 'spm@cundd.net',
				'name' => 'Superman',
			),
		);
		$this->assertEquals($testContent, $this->fixture->parse(json_encode($testContent), $dummyRequest));
	}

	/**
	 * @test
	 * @expectedException \Cundd\PersistentObjectStore\Server\Exception\InvalidBodyException
	 */
	public function parseInvalidBodyTest() {
		/** @var Request $dummyRequest */
		$dummyRequest = new JsonBodyParserTest_DummyRequestClass();
		$this->fixture->parse('name":"Daniel","email":"info@cundd.net"}', $dummyRequest);
	}

	/**
	 * @test
	 * @expectedException \Cundd\PersistentObjectStore\Server\Exception\InvalidBodyException
	 */
	public function parseEmptyBodyTest() {
		/** @var Request $dummyRequest */
		$dummyRequest = new JsonBodyParserTest_DummyRequestClass();
		$this->fixture->parse('', $dummyRequest);
	}
}
 