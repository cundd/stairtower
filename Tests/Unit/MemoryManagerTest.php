<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 18.10.14
 * Time: 13:39
 */

namespace Cundd\PersistentObjectStore;

use Cundd\PersistentObjectStore\Domain\Model\Data;
use Cundd\PersistentObjectStore\Utility\DebugUtility;
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
		$object = new Data(array('email' => 'info@cundd.net'));
		$identifier = 'my-identifier';
		MemoryManager::registerObject($object, $identifier);
		$this->assertTrue(MemoryManager::hasObject($identifier));
		$this->assertSame($object, MemoryManager::getObject($identifier));

		$object = new stdClass();
		$identifier = 'my-identifier-2';
		MemoryManager::registerObject($object, $identifier);
		$this->assertTrue(MemoryManager::hasObject($identifier));
		$this->assertSame($object, MemoryManager::getObject($identifier));
	}

	/**
	 * @test
	 */
	public function objectAccessTest() {
		$identifier = 'not-existing-identifier';
		$this->assertFalse(MemoryManager::hasObject($identifier));
		$this->assertFalse(MemoryManager::getObject($identifier));

		$object = new Data(array('email' => 'info@cundd.net'));
		$identifier = 'my-identifier';
		MemoryManager::registerObject($object, $identifier);
		$this->assertTrue(MemoryManager::hasObject($identifier));
		$this->assertSame($object, MemoryManager::getObject($identifier));

		$object = new stdClass();
		$identifier = 'my-identifier-2';
		MemoryManager::registerObject($object, $identifier);
		$this->assertTrue(MemoryManager::hasObject($identifier));
		$this->assertSame($object, MemoryManager::getObject($identifier));
	}

	/**
	 * @test
	 */
	public function freeTest() {
		$startMemory = memory_get_usage(TRUE);
		$identifier1 = 'my-identifier';
		$identifier2 = 'my-identifier-2';
		$identifier3 = 'my-identifier-3';
		$this->createBigData($identifier1, $identifier2, $identifier3);

		$highMemory = memory_get_usage(TRUE);
		$this->assertGreaterThan($startMemory, $highMemory);

		MemoryManager::free($identifier1);
		MemoryManager::free($identifier2);

		$freedMemory = memory_get_usage(TRUE);

		$this->assertFalse(MemoryManager::hasObject($identifier1));
		$this->assertFalse(MemoryManager::hasObject($identifier2));

		// Should actually be less
		$this->assertLessThanOrEqual($highMemory, $freedMemory);
	}

	/**
	 * @test
	 * @expectedException \Cundd\PersistentObjectStore\Exception\MemoryManagerException
	 */
	public function failedFreeTest() {
		$identifier = 'not-existing-identifier';
		MemoryManager::free($identifier);
	}

	/**
	 * @test
	 */
	public function getIdentifiersByTagTest() {
		$object1 = new stdClass();
		$identifier1 = 'my-identifier';
		MemoryManager::registerObject($object1, $identifier1, array('tag1', 'tag2'));

		$object2 = new stdClass();
		$identifier2 = 'my-identifier-2';
		MemoryManager::registerObject($object2, $identifier2, array('tag1', 'tag2', 'tag3'));

		$object3 = new stdClass();
		$identifier3 = 'my-identifier-3';
		MemoryManager::registerObject($object3, $identifier3, array('tag2', 'tag3'));

		$identifiers = MemoryManager::getIdentifiersByTag('tag1');
		$this->assertSame(array($identifier1, $identifier2), $identifiers);
	}

	/**
	 * @test
	 * @expectedException \Cundd\PersistentObjectStore\Exception\MemoryManagerException
	 */
	public function getIdentifierNotExists() {
		$this->assertEmpty(MemoryManager::getIdentifiersByTag('tag' . time(), FALSE));
	}

	/**
	 * @test
	 */
	public function getIdentifierNotExistsGraceful() {
		$this->assertEmpty(MemoryManager::getIdentifiersByTag('tag' . time(), TRUE));
	}

	/**
	 * @test
	 */
	public function getObjectsByTagTest() {
		$object1 = new stdClass();
		$identifier1 = 'my-identifier';
		MemoryManager::registerObject($object1, $identifier1, array('tag1', 'tag2'));

		$object2 = new stdClass();
		$identifier2 = 'my-identifier-2';
		MemoryManager::registerObject($object2, $identifier2, array('tag1', 'tag2', 'tag3'));

		$object3 = new stdClass();
		$identifier3 = 'my-identifier-3';
		MemoryManager::registerObject($object3, $identifier3, array('tag2', 'tag3'));

		$objects = MemoryManager::getObjectsByTag('tag1');
		$this->assertSame(array($object1, $object2), $objects);
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
		$this->assertGreaterThan($startMemory, $highMemory);

		MemoryManager::freeObjectsByTag('tag1');

		$freedMemory = memory_get_usage(TRUE);

		$this->assertFalse(MemoryManager::hasObject($identifier1));
		$this->assertFalse(MemoryManager::hasObject($identifier2));

		// Should actually be less
		$this->assertLessThanOrEqual($highMemory, $freedMemory);
	}

	/**
	 * @test
	 */
	public function cleanupTest() {
		MemoryManager::cleanup();
	}

	/**
	 * @param $identifier1
	 * @param $identifier2
	 * @param $identifier3
	 */
	protected function createBigData($identifier1, $identifier2, $identifier3) {
		$object = new Data(array('email' => 'info@cundd.net'));
		MemoryManager::registerObject($object, $identifier1);
		$this->assertTrue(MemoryManager::hasObject($identifier1));
		$this->assertSame($object, MemoryManager::getObject($identifier1));

		$object = new stdClass();
		$object->data = file_get_contents($this->checkPersonFile());
		MemoryManager::registerObject($object, $identifier2);
		$this->assertTrue(MemoryManager::hasObject($identifier2));
		$this->assertSame($object, MemoryManager::getObject($identifier2));

		$object = new stdClass();
		MemoryManager::registerObject($object, $identifier3);
		$this->assertTrue(MemoryManager::hasObject($identifier3));
		$this->assertSame($object, MemoryManager::getObject($identifier3));
	}

}
 