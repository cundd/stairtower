<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 25.08.14
 * Time: 22:00
 */

namespace Cundd\PersistentObjectStore\Configuration;

use Cundd\PersistentObjectStore\RuntimeException;
use Cundd\PersistentObjectStore\Server\ServerInterface;
use Cundd\PersistentObjectStore\Utility\ObjectUtility;
use Monolog\Logger;

/**
 * Configuration Manager class
 *
 * @package Cundd\PersistentObjectStore\Configuration
 */
class ConfigurationManager implements ConfigurationManagerInterface
{
    /**
     * Shared instance
     *
     * @var ConfigurationManagerInterface
     */
    protected static $sharedInstance;

    /**
     * Configuration as array
     *
     * @var array
     */
    protected $configuration;

    public function __construct()
    {
        $configurationReader = new ConfigurationReader();
        $this->configuration = array_merge_recursive($this->getDefaults(),
            $configurationReader->readConfigurationFiles());

        self::$sharedInstance = $this;
    }

    /**
     * Returns the default configuration
     *
     * @return array
     */
    public function getDefaults()
    {
        $basePath = $this->getBasePath();
        $varPath  = $basePath . 'var/';
        return array(
            'basePath'      => $basePath,
            'binPath'    => $basePath . 'bin/',
            'phpBinPath' => $this->getPhpBinaryPath(),
            'dataPath'      => $varPath . 'Data/',
            'writeDataPath' => $varPath . 'Data/',
            'lockPath'      => $varPath . 'Lock/',
            'cachePath'     => $varPath . 'Cache/',
            'logPath'       => $varPath . 'Log/',
            'tempPath'      => $varPath . 'Temp/',
            'rescuePath'    => $varPath . 'Rescue/',
            'logLevel'   => Logger::INFO,
            'serverMode' => ServerInterface::SERVER_MODE_NOT_RUNNING
        );
    }

    /**
     * Returns the path to the installation
     *
     * @return string
     */
    public function getBasePath()
    {
        static $basePath;
        if (!$basePath) {
            $basePath = (realpath(__DIR__ . '/../../') ?: __DIR__ . '/../..') . '/';
        }
        return $basePath;
    }

    /**
     * Returns PHP's binary path
     *
     * @return string
     */
    public function getPhpBinaryPath()
    {
        $phpBinPath = defined('PHP_BINARY') ? PHP_BINARY : PHP_BINDIR . '/php';
        if (defined('HHVM_VERSION')) {
            $phpBinPath = (false !== ($hhvm = getenv('PHP_BINARY')) ? $hhvm : PHP_BINARY) . ' --php';
        }
        return $phpBinPath;
    }

    /**
     * Returns the shared instance
     *
     * @return ConfigurationManagerInterface
     */
    public static function getSharedInstance()
    {
        if (!self::$sharedInstance) {
            new static();
        }
        return self::$sharedInstance;
    }

    /**
     * Returns the configuration for the given key path
     *
     * @param string $keyPath
     * @return mixed
     */
    public function getConfigurationForKeyPath($keyPath)
    {
        return ObjectUtility::valueForKeyPathOfObject($keyPath, $this->configuration);
    }

    /**
     * Sets the configuration for the given key path
     *
     * @param string $keyPath
     * @param mixed  $value
     * @return $this
     */
    public function setConfigurationForKeyPath($keyPath, $value)
    {
        if (strpos($keyPath, '.') !== false) {
            throw new RuntimeException('Dot notation is currently not supported');
        }
        $this->configuration[$keyPath] = $value;
        return $this;
    }

    /**
     * Returns the map from events to classes and methods
     *
     * @return array
     */
    protected function getEventToClassMap()
    {

    }

} 