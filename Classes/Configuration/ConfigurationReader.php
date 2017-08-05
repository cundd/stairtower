<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Configuration;

/**
 * Class to read the configuration from config files
 */
class ConfigurationReader
{
    /**
     * Reads the configuration from the configuration files
     *
     * @return array
     */
    public function readConfigurationFiles():array
    {
        $configuration = $this->readConfigurationFile('/etc/pos');
        if (isset($_ENV['HOME']) && $_ENV['HOME']) {
            $configuration = array_replace_recursive(
                $configuration,
                $this->readConfigurationFile($_ENV['HOME'] . '/.pos/config.json')
            );
        }
        $configuration = array_replace_recursive($configuration, $this->readConfigurationFile('.pos-config.json'));

        return $configuration;
    }

    /**
     * Tries to read the given configuration file
     *
     * @param string $file
     * @return array
     */
    public function readConfigurationFile(string $file): array
    {
        $configuration = [];
        if (file_exists($file) && is_readable($file)) {
            $configuration = json_decode(file_get_contents($file), true);
        }

        return $configuration ? $configuration : [];
    }
}