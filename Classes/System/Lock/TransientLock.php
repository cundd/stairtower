<?php
declare(strict_types=1);

namespace Cundd\Stairtower\System\Lock;

/**
 * Transient in-memory based lock implementation
 */
class TransientLock extends AbstractLock
{
    /**
     * All current locks
     *
     * @var array
     */
    protected static $locks = [];

    /**
     * Locks a lock. Only for internal use.
     *
     * @return    boolean    Returns if the lock could be acquired
     */
    protected function lockInternal()
    {
        self::$locks[$this->getName()] = true;

        return true;
    }

    /**
     * Relinquishes a previously acquired lock. Only for internal use.
     *
     * @return    boolean    Returns if the lock could be relinquished
     */
    protected function unlockInternal()
    {
        if ($this->isLocked()) {
            unset(self::$locks[$this->getName()]);
        }

        return true;
    }

    /**
     * Returns if the lock is currently locked
     *
     * @return bool
     */
    public function isLocked(): bool
    {
        return isset(self::$locks[$this->getName()]);
    }

} 