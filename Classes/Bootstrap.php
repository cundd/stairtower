<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 15.08.14
 * Time: 19:37
 */

namespace Cundd\PersistentObjectStore;

use Cundd\PersistentObjectStore\Configuration\ConfigurationManager;
use DI\ContainerBuilder;
use Doctrine\Common\Cache\FilesystemCache;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Class Bootstrap
 *
 * @package Cundd\PersistentObjectStore
 */
class Bootstrap
{
    /**
     * Dependency injection container
     *
     * @var \DI\Container
     */
    protected $diContainer;

    public function __construct()
    {
        $this->init();
    }

    /**
     * Sets up the environment
     */
    public function init()
    {
        // Set the configured timezone
        $timezone = ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('date.timezone');
        if ($timezone) {
            date_default_timezone_set($timezone);
        }

        $this->getDiContainer();
        $this->initializeLogger();
    }

    /**
     * Returns the dependency injection container
     *
     * @return \DI\Container
     */
    public function getDiContainer()
    {
        if (!$this->diContainer) {
            $builder = new ContainerBuilder();
//			$builder->setDefinitionCache(new ArrayCache());

            $builder->setDefinitionCache(
                new FilesystemCache(
                    ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('cachePath')
                )
            );
            $this->diContainer = $builder->build();
            $builder->addDefinitions(__DIR__ . '/Configuration/dependencyInjectionConfiguration.php');
            $this->diContainer = $builder->build();
//			$this->diContainer = ContainerBuilder::buildDevContainer();
        }
        return $this->diContainer;
    }

    /**
     * Initializes the logger instance
     */
    public function initializeLogger()
    {
        $configurationManager = ConfigurationManager::getSharedInstance();
        $logFileDirectory     = $configurationManager->getConfigurationForKeyPath('logPath');
//        $logFilePath      = $logFileDirectory . 'log-' . getmypid() . '.log';
        $logFilePath = $logFileDirectory . 'log-' . gmdate('Y-m-d') . '.log';
        $logLevel    = $configurationManager->getConfigurationForKeyPath('logLevel');
        $logger      = new Logger('core');

        $logger->pushHandler(new StreamHandler($logFilePath, $logLevel));
        $logger->pushHandler(new StreamHandler(STDOUT, $logLevel));
        $this->getDiContainer()->set('Psr\\Log\\LoggerInterface', $logger);
    }
}