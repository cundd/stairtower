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
class Factory
{
    /**
     * Implementation class for locks
     *
     * @var string
     */
    protected static $lockImplementationClass = 'Cundd\\PersistentObjectStore\\System\\Lock\\FileLock';

    /**
     * Creates a new Lock instance
     *
     * @param string $name Name of a named lock
     * @return LockInterface
     */
    public static function createLock($name = null)
    {
        $class = (string)static::$lockImplementationClass;
        return new $class($name);
    }

    /**
     * Defines the implementation for locks
     *
     * @param string $className
     */
    public static function setLockImplementationClass($className)
    {
        static::$lockImplementationClass = $className;
    }
} 