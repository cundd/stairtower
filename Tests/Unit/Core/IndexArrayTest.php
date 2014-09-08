<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 08.09.14
 * Time: 11:35
 */

namespace Cundd\PersistentObjectStore\Core;

/**
 * Tests for class IndexArray
 *
 * @package Cundd\PersistentObjectStore\Core
 */
class IndexArrayTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var IndexArray
	 */
	protected $fixture;

	protected $testData = array(
		'Daniel',
		'Lisa',
		'Yvonne',
		'Hubert',
		'Alfons',
		'Steve',
		'Bob',
	);

	protected function setUp() {
		$this->fixture = new IndexArray();
		$this->fixture->initWithArray($this->testData);
	}

	protected function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function getFirstTest() {
		$this->assertEquals('Daniel', $this->fixture->first());
	}

	/**
	 * @test
	 */
	public function getLastTest() {
		$this->assertEquals('Bob', $this->fixture->last());
	}

	/**
	 * @test
	 */
	public function countTest() {
		$this->assertSame(7, $this->fixture->count());
	}

	/**
	 * @test
	 */
	public function pushTest() {
		$this->fixture->push('Elvis');
		$this->assertEquals('Elvis', $this->fixture->last());
		$this->assertSame(8, $this->fixture->count());
	}

	/**
	 * @test
	 */
	public function popTest() {
		$this->assertEquals('Bob', $this->fixture->pop());
		$this->assertEquals('Steve', $this->fixture->last());
		$this->assertSame(6, $this->fixture->count());

		$this->assertEquals('Steve', $this->fixture->pop());
		$this->assertEquals('Alfons', $this->fixture->last());
		$this->assertSame(5, $this->fixture->count());

		$this->fixture->push('Bob');
		$this->assertEquals('Bob', $this->fixture->last());
		$this->assertSame(6, $this->fixture->count());
	}

	/**
	 * @test
	 */
	public function currentTest() {
		$this->assertEquals('Daniel', $this->fixture->current());
	}

	/**
	 * @test
	 */
	public function nextTest() {
		$this->assertEquals('Daniel', $this->fixture->current());
		$this->fixture->next();
		$this->assertEquals('Lisa', $this->fixture->current());
	}

	/**
	 * @test
	 */
	public function keyTest() {
		$this->assertEquals(0, $this->fixture->key());
		$this->fixture->next();
		$this->assertEquals(1, $this->fixture->key());
	}

	/**
	 * @test
	 */
	public function validTest() {
		$this->assertTrue($this->fixture->valid());
		$this->fixture->next();
		$this->assertTrue($this->fixture->valid());
		$this->fixture->next();
		$this->assertTrue($this->fixture->valid());
		$this->fixture->next();
		$this->assertTrue($this->fixture->valid());
		$this->fixture->next();
		$this->assertTrue($this->fixture->valid());
		$this->fixture->next();
		$this->assertTrue($this->fixture->valid());
		$this->fixture->next();
		$this->assertTrue($this->fixture->valid());
		$this->fixture->next();

		$this->assertFalse($this->fixture->valid());
	}

	/**
	 * @test
	 */
	public function rewindTest() {
		$this->assertEquals('Daniel', $this->fixture->current());
		$this->fixture->next();
		$this->fixture->next();
		$this->assertEquals('Yvonne', $this->fixture->current());

		$this->fixture->rewind();
		$this->assertEquals('Daniel', $this->fixture->current());
	}

	/**
	 * @test
	 */
	public function offsetExistsTest() {
		$this->assertTrue($this->fixture->offsetExists(0));
		$this->assertTrue($this->fixture->offsetExists(1));
		$this->assertTrue($this->fixture->offsetExists(2));
		$this->assertTrue($this->fixture->offsetExists(3));
		$this->assertTrue($this->fixture->offsetExists(4));
		$this->assertTrue($this->fixture->offsetExists(5));
		$this->assertTrue($this->fixture->offsetExists(6));

		$this->assertFalse($this->fixture->offsetExists(7));
	}

	/**
	 * @test
	 */
	public function offsetGetTest() {
		$this->assertEquals('Daniel', $this->fixture->offsetGet(0));
		$this->assertEquals('Lisa', $this->fixture->offsetGet(1));
		$this->assertEquals('Yvonne', $this->fixture->offsetGet(2));
	}

	/**
	 * @test
	 */
	public function offsetSetTest() {
		$this->fixture->offsetSet(0, 'Sture');
		$this->assertEquals('Sture', $this->fixture->offsetGet(0));

		$this->fixture->offsetSet(1, 'Lars');
		$this->assertEquals('Lars', $this->fixture->offsetGet(1));

		$this->fixture->offsetSet(2, 'Sandra');
		$this->assertEquals('Sandra', $this->fixture->offsetGet(2));
	}

	/**
	 * @test
	 */
	public function offsetUnsetTest() {
		$this->fixture->offsetUnset(0);
		$this->assertNull($this->fixture->offsetGet(0));
		$this->assertNotNull($this->fixture->offsetGet(1));

		$this->fixture->offsetUnset(2);
		$this->assertNull($this->fixture->offsetGet(2));
	}


}
 