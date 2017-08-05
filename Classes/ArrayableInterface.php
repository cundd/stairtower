<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 14.10.14
 * Time: 21:38
 */

namespace Cundd\PersistentObjectStore;

use SplFixedArray;

/**
 * Interface that allows transformation to arrays and fixed array
 *
 * @package Cundd\PersistentObjectStore
 */
interface ArrayableInterface
{
    /**
     * Returns the filtered items as array
     *
     * @return array
     */
    public function toArray();

    /**
     * Returns the filtered items as fixed array
     *
     * @return SplFixedArray
     */
    public function toFixedArray();
} 