<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 15.08.14
 * Time: 19:37
 */

namespace Cundd\PersistentObjectStore\Bootstrap;

use Cundd\PersistentObjectStore\Configuration\ConfigurationManager;
use DI\ContainerBuilder;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\FilesystemCache;

/**
 * Class Core
 *
 * @package Cundd\PersistentObjectStore
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

        $this->getDiContainer();
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
            $builder->setDefinitionCache(
                new FilesystemCache(ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('cachePath'))
            );
            var_dump(ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('cachePath'));
            //$builder->setDefinitionCache(new ArrayCache());

            $this->diContainer = $builder->build();
            $builder->addDefinitions(__DIR__ . '/../Configuration/dependencyInjectionConfiguration.php');
            $this->diContainer = $builder->build();
        }
        return $this->diContainer;
    }
}