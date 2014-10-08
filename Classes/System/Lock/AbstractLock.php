<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 08.10.14
 * Time: 11:14
 */

namespace Cundd\PersistentObjectStore\System\Lock;

/**
 * Abstract lock implementation
 *
 * @package Cundd\PersistentObjectStore\System
 */
abstract class AbstractLock implements LockInterface {
	/**
	 * Identifier for the lock
	 *
	 * @var string
	 */
	protected $name;

	function __construct($name = NULL) {
		$this->name = $name;
	}

	/**
	 * Attempts to acquire a lock, blocking a thread’s execution until the lock can be acquired
	 *
	 * @return void
	 */
	public function lock() {
		while ($this->isLocked()) {
			usleep(10);
		}
		$this->_lock();
	}

	/**
	 * Relinquishes a previously acquired lock
	 *
	 * @return void
	 */
	public function unlock() {
		$this->_unlock();
	}

	/**
	 * Attempts to acquire a lock and immediately returns a Boolean value that indicates whether the attempt was
	 * successful
	 *
	 * @return bool
	 */
	public function tryLock() {
		if ($this->isLocked()) return FALSE;

		$this->_lock();
		return TRUE;
	}

	/**
	 * Returns the identifier of the named lock
	 *
	 * This can be used to get an exclusive lock that is defined through this identifier
	 *
	 * @return string
	 */
	public function getName() {
		if (!$this->name) {
			return (string)getmypid();
		}
		return $this->name;
	}

	function __destruct() {
		$this->_unlock();
	}


	/**
	 * Locks a lock. Only for internal use.
	 * @return	boolean	Returns if the lock could be acquired
	 */
	abstract protected function _lock();

	/**
	 * Relinquishes a previously acquired lock. Only for internal use.
	 *
	 * @return	boolean	Returns if the lock could be relinquished
	 */
	abstract protected function _unlock();
} 