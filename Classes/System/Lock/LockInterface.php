<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 08.10.14
 * Time: 11:08
 */

namespace Cundd\PersistentObjectStore\System\Lock;

/**
 * Interface for locks
 *
 * @package Cundd\PersistentObjectStore\System
 */
interface LockInterface {
	/**
	 * Attempts to acquire a lock, blocking a thread’s execution until the lock can be acquired
	 *
	 * @return void
	 */
	public function lock();

	/**
	 * Relinquishes a previously acquired lock
	 *
	 * @return void
	 */
	public function unlock();

	/**
	 * Attempts to acquire a lock and immediately returns a Boolean value that indicates whether the attempt was
	 * successful
	 *
	 * @return bool
	 */
	public function tryLock();

	/**
	 * Returns if the lock is currently locked
	 *
	 * @return bool
	 */
	public function isLocked();
}