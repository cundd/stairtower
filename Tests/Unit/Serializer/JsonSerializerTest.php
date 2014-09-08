<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 24.08.14
 * Time: 22:48
 */

namespace Cundd\PersistentObjectStore\Serializer;


class JsonSerializerTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var JsonSerializer
	 */
	protected $fixture;

	public function setUp(){
		parent::setUp();
		$this->fixture = new JsonSerializer();
	}

	/**
	 * @test
	 */
	public function serializationTest() {
		$input = NULL;
		$this->assertSame($input, $this->fixture->unserialize($this->fixture->serialize($input)));

		$input = 'A string';
		$this->assertSame($input, $this->fixture->unserialize($this->fixture->serialize($input)));

		$input = 0.999009;
		$this->assertSame($input, $this->fixture->unserialize($this->fixture->serialize($input)));

		$input = -0.999009;
		$this->assertSame($input, $this->fixture->unserialize($this->fixture->serialize($input)));

		$input = 5;
		$this->assertSame($input, $this->fixture->unserialize($this->fixture->serialize($input)));

		$input = PHP_INT_MAX;
		$this->assertSame($input, $this->fixture->unserialize($this->fixture->serialize($input)));

		$input = 0;
		$this->assertSame($input, $this->fixture->unserialize($this->fixture->serialize($input)));

		$input = -100;
		$this->assertSame($input, $this->fixture->unserialize($this->fixture->serialize($input)));

		$input = TRUE;
		$this->assertSame($input, $this->fixture->unserialize($this->fixture->serialize($input)));

		$input = FALSE;
		$this->assertSame($input, $this->fixture->unserialize($this->fixture->serialize($input)));

		$input = array();
		$this->assertSame($input, $this->fixture->unserialize($this->fixture->serialize($input)));

		$input = new \stdClass();
		$input->firstName = 'Daniel';
		$input->lastName = 'Corn';
		$this->assertEquals(get_object_vars($input), $this->fixture->unserialize($this->fixture->serialize($input)));

		$input = array(1 => 'A', 2 => 'B', 3 => 'C');
		$this->assertEquals($input, $this->fixture->unserialize($this->fixture->serialize($input)));

		$input = range('A', 9);
		$input = array_rand($input, count($input));
		$this->assertEquals($input, $this->fixture->unserialize($this->fixture->serialize($input)));
	}
}
 