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
class FileLock extends AbstractLock
{
    /**
     * Locks a lock. Only for internal use.
     *
     * @return    boolean    Returns if the lock could be acquired
     */
    protected function lockInternal()
    {
        return touch($this->getLockPath());
    }

    /**
     * Returns the path to the lock file.
     *
     * @return    string
     */
    protected function getLockPath()
    {
        $lockPath = ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('lockPath');

        static $lockPathExists = -1;
        if ($lockPathExists === -1) {
            if (!file_exists($lockPath)) {
                mkdir($lockPath, 0777, true);
            }
            $lockPathExists = true;
        }
        return $lockPath . 'lock_' . sha1($this->getName());
    }

    /**
     * Relinquishes a previously acquired lock. Only for internal use.
     *
     * @return    boolean    Returns if the lock could be relinquished
     */
    protected function unlockInternal()
    {
        if ($this->isLocked()) {
            return unlink($this->getLockPath());
        }
        return true;
    }

    /**
     * Returns if the lock is currently locked
     *
     * @return bool
     */
    public function isLocked()
    {
        return file_exists($this->getLockPath());
    }
}
