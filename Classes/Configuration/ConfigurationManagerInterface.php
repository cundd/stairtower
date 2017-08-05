<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Configuration;

/**
 * Interface for Configuration Managers
 */
interface ConfigurationManagerInterface
{
    /**
     * Returns the configuration for the given key path
     *
     * @param string $keyPath
     * @return mixed
     */
    public function getConfigurationForKeyPath(string $keyPath);

    /**
     * Sets the configuration for the given key path
     *
     * @param string $keyPath
     * @param mixed  $value
     * @return $this
     */
    public function setConfigurationForKeyPath(string $keyPath, $value): self;
}
