<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Bootstrap;

use Cundd\Stairtower\Configuration\ConfigurationManager;
use Cundd\Stairtower\Event\SharedEventEmitter;
use Cundd\Stairtower\Exception\InvalidArgumentError;
use DI\ContainerBuilder;

/**
 * Core bootstrapping class
 */
class Core
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

        $this->initializeGlobalFileHandles();
        $this->getDiContainer();
        $this->getSharedEventEmitter();
        $this->callStartupMethods();
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
            $builder->useAnnotations(true);
            $builder->setDefinitionCache(
            //new \Doctrine\Common\Cache\FilesystemCache(ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('cachePath'))
                new \Doctrine\Common\Cache\ArrayCache()
            );

            $builder->addDefinitions(__DIR__ . '/../Configuration/dependencyInjectionConfiguration.php');
            $this->diContainer = $builder->build();
        }

        return $this->diContainer;
    }

    /**
     * Instantiates the shared Event Emitter
     *
     * @return SharedEventEmitter
     */
    public function getSharedEventEmitter()
    {
        return $this->getDiContainer()->get(SharedEventEmitter::class);
    }

    /**
     * Call the registered startup methods
     */
    private function callStartupMethods()
    {
        $startupMethods = ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('startupMethods');
        if ($startupMethods) {
            foreach ($startupMethods as $startupMethod) {
                $classAndMethod = explode('::', $startupMethod, 2);
                if (count($classAndMethod) < 2) {
                    throw new InvalidArgumentError(
                        'Startup methods must be defined in the format \class\name::method',
                        1448974563
                    );
                }
                $instance = $this->getDiContainer()->get($classAndMethod[0]);
                $method = $classAndMethod[1];
                call_user_func([$instance, $method]);
            }
        }
    }

    /**
     * Initializes the standard IO file handles
     */
    protected function initializeGlobalFileHandles()
    {
        if (!defined('STDIN')) {
            define('STDIN', fopen('php://stdin', 'r'));
        }
        if (!defined('STDOUT')) {
            define('STDOUT', fopen('php://stdout', 'w'));
        }
        if (!defined('STDERR')) {
            define('STDERR', fopen('php://stderr', 'w'));
        }
    }
}
