<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 08.10.14
 * Time: 11:42
 */

namespace Cundd\PersistentObjectStore\System\Lock;


use Cundd\PersistentObjectStore\AbstractCase;


class FileLockWithAccessToFilePath extends FileLock {
	/**
	 * Returns the path to the lock file.
	 *
	 * @return	string
	 */
	public function getLockPath() {
		return $this->_getLockPath();
	}
}

/**
 * Class FileLockTest
 *
 * @package Cundd\PersistentObjectStore\System\Lock
 */
class FileLockTest extends AbstractCase {
	/**
	 * @var LockInterface
	 */
	protected $fixture;

	protected function tearDown() {
		unset($this->fixture);

		parent::tearDown();
	}

	/**
	 * @test
	 */
	public function lockTest() {
		$this->assertFalse($this->fixture->isLocked());
		$this->fixture->lock();
		$this->assertTrue($this->fixture->isLocked());
		$this->fixture->unlock();
		$this->assertFalse($this->fixture->isLocked());
	}

	/**
	 * @test
	 */
	public function unlockTest() {
		$this->fixture = new FileLockWithAccessToFilePath();
		$lockPath = $this->fixture->getLockPath();

		$this->assertFalse($this->fixture->isLocked());
		$this->fixture->lock();
		$this->assertTrue($this->fixture->isLocked());
		$this->fixture->unlock();
		$this->assertFalse($this->fixture->isLocked());
		$this->assertFileNotExists($lockPath);

		$this->fixture->lock();
		$this->assertTrue($this->fixture->isLocked());
		$this->assertFileExists($lockPath);

		unset($this->fixture);
		$this->assertFileNotExists($lockPath);
	}

	/**
	 * @test
	 */
	public function tryLockTest() {
		$this->assertFalse($this->fixture->isLocked());
		$this->fixture->tryLock();
		$this->assertTrue($this->fixture->isLocked());
		$this->fixture->unlock();
		$this->assertFalse($this->fixture->isLocked());
	}
}
 