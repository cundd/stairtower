<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Domain\Model;

/**
 * Trait to manage Database State
 */
trait DatabaseStateTrait
{
    /**
     * Database's current state
     *
     * @var string
     */
    protected $state = DatabaseStateInterface::STATE_CLEAN;

    /**
     * Returns the Database's current state
     *
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * Sets the Database's state
     *
     * @param string $newState
     * @return DatabaseStateInterface
     */
    public function setState(string $newState): DatabaseStateInterface
    {
        $this->state = $newState;

        return $this;
    }
}
