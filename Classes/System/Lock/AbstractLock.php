<?php
declare(strict_types=1);

namespace Cundd\Stairtower\System\Lock;

use Cundd\Stairtower\System\Lock\Exception\TimeoutException;

/**
 * Abstract lock implementation
 */
abstract class AbstractLock implements LockInterface
{
    /**
     * Identifier for the lock
     *
     * @var string
     */
    protected $name;

    /**
     * Locks a lock. Only for internal use.
     *
     * @return    boolean    Returns if the lock could be acquired
     */
    abstract protected function lockInternal();

    /**
     * Relinquishes a previously acquired lock. Only for internal use.
     *
     * @return    boolean    Returns if the lock could be relinquished
     */
    abstract protected function unlockInternal();

    public function __construct($name = null)
    {
        $this->name = $name;
    }

    public function lock()
    {
        while ($this->isLocked()) {
            usleep(10);
        }
        $this->lockInternal();
    }


    public function lockWithTimeout(int $timeout)
    {
        $timeUntilTimeout = $timeout;
        while ($this->isLocked()) {
            if ($timeUntilTimeout <= 0) {
                throw new TimeoutException(
                    sprintf('Could not acquire the lock within %d microseconds', $timeout),
                    1413546617
                );
            }
            $timeUntilTimeout -= 10;
            usleep(10);
        }
        $this->lockInternal();
    }

    public function unlock()
    {
        $this->unlockInternal();
    }

    public function tryLock(): bool
    {
        if ($this->isLocked()) {
            return false;
        }

        $this->lockInternal();

        return true;
    }

    public function getName(): string
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