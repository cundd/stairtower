<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 18.10.14
 * Time: 13:39
 */

namespace Cundd\PersistentObjectStore;

use Cundd\PersistentObjectStore\Domain\Model\Document;
use Cundd\PersistentObjectStore\Memory\Manager;
use stdClass;

/**
 * MemoryManager tests
 *
 * @package Cundd\PersistentObjectStore
 */
class MemoryManagerTest extends AbstractCase {
	protected function setUp() {
		// parent::setUp();
	}

	/**
	 * @test
	 */
	public function registerObjectTest() {
		$object = new Document(array('email' => 'info@cundd.net'));
		$identifier = 'my-identifier';
		Manager::registerObject($object, $identifier);
		$this->assertTrue(Manager::hasObject($identifier));
		$this->assertSame($object, Manager::getObject($identifier));

		$object = new stdClass();
		$identifier = 'my-identifier-2';
		Manager::registerObject($object, $identifier);
		$this->assertTrue(Manager::hasObject($identifier));
		$this->assertSame($object, Manager::getObject($identifier));
	}

	/**
	 * @test
	 */
	public function objectAccessTest() {
		$identifier = 'not-existing-identifier';
		$this->assertFalse(Manager::hasObject($identifier));
		$this->assertFalse(Manager::getObject($identifier));

		$object = new Document(array('email' => 'info@cundd.net'));
		$identifier = 'my-identifier';
		Manager::registerObject($object, $identifier);
		$this->assertTrue(Manager::hasObject($identifier));
		$this->assertSame($object, Manager::getObject($identifier));

		$object = new stdClass();
		$identifier = 'my-identifier-2';
		Manager::registerObject($object, $identifier);
		$this->assertTrue(Manager::hasObject($identifier));
		$this->assertSame($object, Manager::getObject($identifier));
	}

	/**
	 * @test
	 */
	public function freeTest() {
		$startMemory = memory_get_usage(TRUE);
		$identifier1 = 'my-new-identifier';
		$identifier2 = 'my-new-identifier-2';
		$identifier3 = 'my-new-identifier-3';
		$this->createBigData($identifier1, $identifier2, $identifier3);

		$highMemory = memory_get_usage(TRUE);
		//$this->assertGreaterThan($startMemory, $highMemory);

		Manager::free($identifier1);
		Manager::free($identifier2);

		$freedMemory = memory_get_usage(TRUE);

		$this->assertFalse(Manager::hasObject($identifier1));
		$this->assertFalse(Manager::hasObject($identifier2));

		// Should actually be less
		//$this->assertLessThanOrEqual($highMemory, $freedMemory);
	}

	/**
	 * @test
	 * @expectedException \Cundd\PersistentObjectStore\Memory\Exception\ManagerException
	 */
	public function failedFreeTest() {
		$identifier = 'not-existing-identifier';
		Manager::free($identifier);
	}

	/**
	 * @test
	 */
	public function getIdentifiersByTagTest() {
		$object1 = new stdClass();
		$identifier1 = 'my-identifier';
		Manager::registerObject($object1, $identifier1, array('tag1', 'tag2'));

		$object2 = new stdClass();
		$identifier2 = 'my-identifier-2';
		Manager::registerObject($object2, $identifier2, array('tag1', 'tag2', 'tag3'));

		$object3 = new stdClass();
		$identifier3 = 'my-identifier-3';
		Manager::registerObject($object3, $identifier3, array('tag2', 'tag3'));

		$identifiers = Manager::getIdentifiersByTag('tag1');
		$this->assertSame(array($identifier1, $identifier2), $identifiers);
	}

	/**
	 * @test
	 * @expectedException \Cundd\PersistentObjectStore\Memory\Exception\ManagerException
	 */
	public function getIdentifierNotExists() {
		$this->assertEmpty(Manager::getIdentifiersByTag('tag' . time(), FALSE));
	}

	/**
	 * @test
	 */
	public function getIdentifierNotExistsGraceful() {
		$this->assertEmpty(Manager::getIdentifiersByTag('tag' . time(), TRUE));
	}

	/**
	 * @test
	 */
	public function getObjectsByTagTest() {
		$object1 = new stdClass();
		$identifier1 = 'my-identifier';
		Manager::registerObject($object1, $identifier1, array('tag1', 'tag2'));

		$object2 = new stdClass();
		$identifier2 = 'my-identifier-2';
		Manager::registerObject($object2, $identifier2, array('tag1', 'tag2', 'tag3'));

		$object3 = new stdClass();
		$identifier3 = 'my-identifier-3';
		Manager::registerObject($object3, $identifier3, array('tag2', 'tag3'));

		$objects = Manager::getObjectsByTag('tag1');
		$this->assertSame(array(
			$identifier1 => $object1,
			$identifier2 => $object2
		), $objects);
	}

	/**
	 * @test
	 */
	public function freeObjectsByTagTest() {
		$startMemory = memory_get_usage(TRUE);
		$identifier1 = 'my-identifier';
		$identifier2 = 'my-identifier-2';
		$identifier3 = 'my-identifier-3';
		$this->createBigData($identifier1, $identifier2, $identifier3);

		$highMemory = memory_get_usage(TRUE);
		//$this->assertGreaterThan($startMemory, $highMemory);

		Manager::freeObjectsByTag('tag1');

		$freedMemory = memory_get_usage(TRUE);

		$this->assertFalse(Manager::hasObject($identifier1));
		$this->assertFalse(Manager::hasObject($identifier2));

		// Should actually be less
		//$this->assertLessThanOrEqual($highMemory, $freedMemory);
	}

	/**
	 * @test
	 */
	public function cleanupTest() {
		Manager::cleanup();
	}

	/**
	 * @param $identifier1
	 * @param $identifier2
	 * @param $identifier3
	 */
	protected function createBigData($identifier1, $identifier2, $identifier3) {
		$object = new Document(array('email' => 'info@cundd.net'));
		Manager::registerObject($object, $identifier1, array('tag1', 'tag2'));
		$this->assertTrue(Manager::hasObject($identifier1));
		$this->assertSame($object, Manager::getObject($identifier1));

		$object = new stdClass();
		$object->data = file_get_contents($this->checkPersonFile());
		Manager::registerObject($object, $identifier2, array('tag1', 'tag2', 'tag3'));
		$this->assertTrue(Manager::hasObject($identifier2));
		$this->assertSame($object, Manager::getObject($identifier2));

		$object = new stdClass();
		Manager::registerObject($object, $identifier3, array('tag2', 'tag3'));
		$this->assertTrue(Manager::hasObject($identifier3));
		$this->assertSame($object, Manager::getObject($identifier3));
	}

}
 