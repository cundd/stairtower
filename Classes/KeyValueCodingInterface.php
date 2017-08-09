<?php
declare(strict_types=1);

namespace Cundd\Stairtower;


interface KeyValueCodingInterface
{
    /**
     * Returns the value for the given key from the data
     *
     * @param string $key
     * @return mixed
     */
    public function valueForKey(string $key);

    /**
     * Sets the value for the given key from the data
     *
     * @param mixed  $value
     * @param string $key
     * @return
     */
    public function setValueForKey($value, string $key);


    /**
     * Returns the value for the given key path from the data
     *
     * @param string $keyPath
     * @return mixed
     */
    public function valueForKeyPath(string $keyPath);
} 