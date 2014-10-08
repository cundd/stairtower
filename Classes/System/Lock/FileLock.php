<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 08.10.14
 * Time: 11:22
 */

namespace Cundd\PersistentObjectStore\System\Lock;

use Cundd\PersistentObjectStore\Configuration\ConfigurationManager;

/**
 * File based lock implementation
 *
 * @package Cundd\PersistentObjectStore\System\Lock
 */
class FileLock extends AbstractLock {
	/**
	 * Returns if the lock is currently locked
	 *
	 * @return bool
	 */
	public function isLocked() {
		return file_exists($this->_getLockPath());
	}

	/**
	 * Locks a lock. Only for internal use.
	 * @return	boolean	Returns if the lock could be acquired
	 */
	protected function _lock() {
		return touch($this->_getLockPath());
	}

	/**
	 * Relinquishes a previously acquired lock. Only for internal use.
	 *
	 * @return	boolean	Returns if the lock could be relinquished
	 */
	protected function _unlock() {
		if ($this->isLocked()) {
			return unlink($this->_getLockPath());
		}
		return TRUE;
	}

	/**
	 * Returns the path to the lock file.
	 *
	 * @return	string
	 */
	protected function _getLockPath() {
		$lockPath = ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('lockPath');

		static $lockPathExists = -1;
		if ($lockPathExists === -1) {
			if (!file_exists($lockPath)) {
				mkdir($lockPath, 0777, TRUE);
			}
			$lockPathExists = TRUE;
		}
		return $lockPath . 'lock_' . $this->getId();
	}

} 