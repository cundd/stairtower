<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 20.09.14
 * Time: 10:44
 */

namespace Cundd\PersistentObjectStore\Domain\Model;


/**
 * Interface for Database implementations
 *
 * @package Cundd\PersistentObjectStore\Domain\Model
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
    public function getState();

    /**
     * Sets the Database's state
     *
     * @param string $newState
     * @return $this
     */
    public function setState($newState);
} 