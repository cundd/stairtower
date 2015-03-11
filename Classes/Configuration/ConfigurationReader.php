<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 08.09.14
 * Time: 16:14
 */

namespace Cundd\PersistentObjectStore\Configuration;

/**
 * Class to read the configuration from config files
 *
 * @package Cundd\PersistentObjectStore\Configuration
 */
class ConfigurationReader
{
    /**
     * Reads the configuration from the configuration files
     *
     * @return array
     */
    public function readConfigurationFiles()
    {
        $configuration = array_merge_recursive(
            $this->readConfigurationFile('/etc/pos')
        );
        if (isset($_ENV['HOME']) && $_ENV['HOME']) {
            $configuration = array_merge_recursive($configuration,
                $this->readConfigurationFile($_ENV['HOME'] . '/.pos/config.json'));
        }
        $configuration = array_merge_recursive($configuration, $this->readConfigurationFile('.pos-config.json'));
        return $configuration;
    }

    /**
     * Tries to read the given configuration file
     *
     * @param string $file
     * @return array
     */
    public function readConfigurationFile($file)
    {
        $configuration = array();
        if (file_exists($file) && is_readable($file)) {
            $configuration = json_decode(file_get_contents($file), true);
        }
        return $configuration ? $configuration : array();
    }
}
