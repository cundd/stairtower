<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 08.10.14
 * Time: 11:14
 */

namespace Cundd\PersistentObjectStore\System\Lock;

use Cundd\PersistentObjectStore\System\Lock\Exception\TimeoutException;

/**
 * Abstract lock implementation
 *
 * @package Cundd\PersistentObjectStore\System
 */
abstract class AbstractLock implements LockInterface
{
    /**
     * Identifier for the lock
     *
     * @var string
     */
    protected $name;

    public function __construct($name = null)
    {
        $this->name = $name;
    }

    /**
     * Attempts to acquire a lock, blocking a thread’s execution until the lock can be acquired
     *
     * @return void
     */
    public function lock()
    {
        while ($this->isLocked()) {
            usleep(10);
        }
        $this->lockInternal();
    }

    /**
     * Locks a lock. Only for internal use.
     *
     * @return    boolean    Returns if the lock could be acquired
     */
    abstract protected function lockInternal();

    /**
     * Attempts to acquire a lock, blocking a thread’s execution until the lock can be acquired or the timeout is reached
     *
     * @param int $timeout Microseconds to wait before throwing a TimeoutException
     * @return void
     * @throws \Cundd\PersistentObjectStore\System\Lock\Exception\TimeoutException if the timeout is reached before the lock can be acquired
     */
    public function lockWithTimeout($timeout)
    {
        $timeUntilTimeout = $timeout;
        while ($this->isLocked()) {
            if ($timeUntilTimeout <= 0) {
                throw new TimeoutException(sprintf('Could not acquire the lock within %d microseconds', $timeout),
                    1413546617);
            }
            $timeUntilTimeout -= 10;
            usleep(10);
        }
        $this->lockInternal();
    }

    /**
     * Relinquishes a previously acquired lock
     *
     * @return void
     */
    public function unlock()
    {
        $this->unlockInternal();
    }

    /**
     * Relinquishes a previously acquired lock. Only for internal use.
     *
     * @return    boolean    Returns if the lock could be relinquished
     */
    abstract protected function unlockInternal();

    /**
     * Attempts to acquire a lock and immediately returns a Boolean value that indicates whether the attempt was
     * successful
     *
     * @return bool
     */
    public function tryLock()
    {
        if ($this->isLocked()) {
            return false;
        }

        $this->lockInternal();
        return true;
    }

    /**
     * Returns the identifier of the named lock
     *
     * This can be used to get an exclusive lock that is defined through this identifier
     *
     * @return string
     */
    public function getName()
    {
        if ($this->name === null) {
            return (string)getmypid();
        }
        return $this->name;
    }

    function __destruct()
    {
        $this->unlockInternal();
    }
}