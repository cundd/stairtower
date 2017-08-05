<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\System\Lock;

/**
 * Interface for locks
 */
interface LockInterface
{
    /**
     * Attempts to acquire a lock, blocking a thread’s execution until the lock can be acquired
     *
     * @return void
     */
    public function lock();

    /**
     * Attempts to acquire a lock, blocking a thread’s execution until the lock can be acquired or the timeout is reached
     *
     * @param int $timeout Microseconds to wait before throwing a TimeoutException
     * @return void
     * @throws \Cundd\PersistentObjectStore\System\Lock\Exception\TimeoutException if the timeout is reached before the lock can be acquired
     */
    public function lockWithTimeout(int $timeout);

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
    public function tryLock(): bool;

    /**
     * Returns if the lock is currently locked
     *
     * @return bool
     */
    public function isLocked(): bool;

    /**
     * Returns the identifier of the named lock
     *
     * This can be used to get an exclusive lock that is defined through this identifier
     *
     * @return string
     */
    public function getName(): string;
}