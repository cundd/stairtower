<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 08.10.14
 * Time: 11:07
 */

namespace Cundd\PersistentObjectStore\System\Lock;

use Cundd\PersistentObjectStore\Configuration\ConfigurationManager;
use Cundd\PersistentObjectStore\Server\ServerInterface;

/**
 * Factory class to retrieve the best lock implementation
 *
 * @package Cundd\PersistentObjectStore\System
 */
class Factory
{
    /**
     * Creates a new Lock instance
     *
     * @param string $name Name of a named lock
     * @return LockInterface
     */
    public static function createLock($name = null)
    {
        if (ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('serverMode') !== ServerInterface::SERVER_MODE_NOT_RUNNING) {
            return new TransientLock($name);
        }
        return new FileLock($name);
    }
} 