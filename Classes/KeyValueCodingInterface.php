<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 24.09.14
 * Time: 08:25
 */

namespace Cundd\PersistentObjectStore;

interface KeyValueCodingInterface
{
    /**
     * Returns the value for the given key from the data
     *
     * @param string $key
     * @return mixed
     */
    public function valueForKey($key);

    /**
     * Sets the value for the given key from the data
     *
     * @param mixed  $value
     * @param string $key
     * @throws LogicException
     */
    public function setValueForKey($value, $key);


    /**
     * Returns the value for the given key path from the data
     *
     * @param string $keyPath
     * @return mixed
     */
    public function valueForKeyPath($keyPath);
}
