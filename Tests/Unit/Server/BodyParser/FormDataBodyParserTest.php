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

class FormDataBodyParserTest_DummyRequestClass {
	public function getPath() {
		return '/contacts/';
	}

	public function getMethod() {
		return 'GET';
	}

}

/**
 * @package Cundd\PersistentObjectStore\Server\BodyParser
 */
class FormDataBodyParserTest extends AbstractCase {
	/**
	 * @var BodyParserInterface
	 */
	protected $fixture;
	/**
	 * @test
	 */
	public function parseBodyTest() {
		/** @var Request $dummyRequest */
		$dummyRequest = new FormDataBodyParserTest_DummyRequestClass();

		$this->assertArrayHasKey('email', $this->fixture->parse('email=test%40cundd.net&name=Daniel', $dummyRequest));
		$this->assertArrayHasKey('email', $this->fixture->parse('name=Daniel&email=test%40cundd.net', $dummyRequest));
		$this->assertArrayHasKey('email', $this->fixture->parse('email=test%40cundd.net', $dummyRequest));
	}
}
 