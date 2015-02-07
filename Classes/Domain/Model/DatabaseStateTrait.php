<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 06.02.15
 * Time: 21:52
 */

namespace Cundd\PersistentObjectStore\Domain\Model;

/**
 * Trait to manage Database State
 *
 * @package Cundd\PersistentObjectStore\Domain\Model
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
    public function getState()
    {
        return $this->state;
    }

    /**
     * Sets the Database's state
     *
     * @param string $newState
     * @return $this
     */
    public function setState($newState)
    {
        $this->state = $newState;
        return $this;
    }
}