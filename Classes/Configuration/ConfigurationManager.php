<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Configuration;

use Cundd\Stairtower\RuntimeException;
use Cundd\Stairtower\Server\ServerInterface;
use Cundd\Stairtower\Utility\ObjectUtility;
use Monolog\Logger;
use Symfony\Component\Process\PhpExecutableFinder;

/**
 * Configuration Manager class
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
        $this->configuration = array_replace_recursive(
            $this->getDefaults(),
            $configurationReader->readConfigurationFiles()
        );

        self::$sharedInstance = $this;
    }

    /**
     * Returns the default configuration
     *
     * @return array
     */
    public function getDefaults(): array
    {
        $basePath = $this->getBasePath();
        $varPath = $basePath . 'var/';
        $installationPath = $this->getInstallationPath();

        return [
            'basePath'         => $basePath,
            'binPath'          => $installationPath . 'bin/',
            'phpBinPath'       => $this->getPhpBinaryPath(),
            'publicResources'  => $basePath . 'Resources/Public/',
            'privateResources' => $basePath . 'Resources/Private/',
            'dataPath'         => $varPath . 'Data/',
            'writeDataPath'    => $varPath . 'Data/',
            'lockPath'         => $varPath . 'Lock/',
            'cachePath'        => $varPath . 'Cache/',
            'logPath'          => $varPath . 'Log/',
            'tempPath'         => $varPath . 'Temp/',
            'rescuePath'       => $varPath . 'Rescue/',
            'logLevel'         => Logger::INFO,
            'serverMode'       => ServerInterface::SERVER_MODE_NOT_RUNNING,
        ];
    }

    /**
     * Returns the path to the base
     *
     * @return string
     */
    public function getBasePath(): string
    {
        static $basePath;
        if (!$basePath) {
            $basePath = $this->getInstallationPath();
            if (file_exists($basePath . '../../autoload.php')) {
                $basePath = (realpath($basePath . '../../../') ?: __DIR__ . '../../..') . '/';
            }
        }

        return $basePath;
    }

    /**
     * Returns the path to the installation
     *
     * @return string
     */
    public function getInstallationPath(): string
    {
        static $installPath;
        if (!$installPath) {
            $installPath = (realpath(__DIR__ . '/../../') ?: __DIR__ . '/../..') . '/';
        }

        return $installPath;
    }

    /**
     * Returns PHP's binary path
     *
     * @return string
     */
    public function getPhpBinaryPath(): string
    {
        $finder = new PhpExecutableFinder();

        return $finder->find();
    }

    /**
     * Returns the shared instance
     *
     * @return ConfigurationManagerInterface
     */
    public static function getSharedInstance(): ConfigurationManagerInterface
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
    public function getConfigurationForKeyPath(string $keyPath)
    {
        return ObjectUtility::valueForKeyPathOfObject($keyPath, $this->configuration);
    }

    /**
     * Sets the configuration for the given key path
     *
     * @param string $keyPath
     * @param mixed  $value
     * @return ConfigurationManagerInterface
     */
    public function setConfigurationForKeyPath(string $keyPath, $value): ConfigurationManagerInterface
    {
        if (strpos($keyPath, '.') !== false) {
            throw new RuntimeException('Dot notation is currently not supported');
        }
        $this->configuration[$keyPath] = $value;

        return $this;
    }
}
