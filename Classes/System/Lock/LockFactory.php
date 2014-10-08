<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 08.10.14
 * Time: 11:07
 */

namespace Cundd\PersistentObjectStore\System\Lock;

/**
 * Factory class to retrieve the best lock implementation
 *
 * @package Cundd\PersistentObjectStore\System
 */
class Factory {
	/**
	 * Creates a new Lock instance
	 *
	 * @return LockInterface
	 */
	static public function createLock() {
		return new FileLock();
	}
} 