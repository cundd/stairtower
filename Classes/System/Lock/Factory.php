<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\System\Lock;

/**
 * Factory class to retrieve the best lock implementation
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
    public static function createLock($name = null): LockInterface
    {
        $class = (string)static::$lockImplementationClass;

        return new $class($name);
    }

    /**
     * Defines the implementation for locks
     *
     * @param string $className
     */
    public static function setLockImplementationClass(string $className)
    {
        static::$lockImplementationClass = $className;
    }
} 