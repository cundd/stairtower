<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Domain\Model;


/**
 * Interface for Database implementations
 */
interface DatabaseStateInterface
{
    /**
     * The Database does not have any changes
     */
    const STATE_CLEAN = 'clean';

    /**
     * The Database has been changed
     */
    const STATE_DIRTY = 'dirty';

    /**
     * Returns the Database's current state
     *
     * @return string
     */
    public function getState(): string;

    /**
     * Sets the Database's state
     *
     * @param string $newState
     * @return DatabaseStateInterface
     */
    public function setState(string $newState): DatabaseStateInterface;
}
