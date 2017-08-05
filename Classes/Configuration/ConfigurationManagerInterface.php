<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 25.08.14
 * Time: 21:56
 */

namespace Cundd\PersistentObjectStore\Configuration;

/**
 * Interface for Configuration Managers
 *
 * @package Cundd\PersistentObjectStore\Configuration
 */
interface ConfigurationManagerInterface
{
    /**
     * Returns the configuration for the given key path
     *
     * @param string $keyPath
     * @return mixed
     */
    public function getConfigurationForKeyPath($keyPath);

    /**
     * Sets the configuration for the given key path
     *
     * @param string $keyPath
     * @param mixed  $value
     * @return $this
     */
    public function setConfigurationForKeyPath($keyPath, $value);
}