<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Bootstrap;

use Cundd\Stairtower\Configuration\ConfigurationManager;
use Cundd\Stairtower\ErrorHandling\CrashHandler;
use Cundd\Stairtower\ErrorHandling\ErrorHandler;

/**
 * Abstract bootstrapping class for the server and router
 */
abstract class AbstractBootstrap
{
    /**
     * Crash handler
     *
     * @var CrashHandler
     */
    protected $crashHandler;

    /**
     * Error handler
     *
     * @var ErrorHandler
     */
    protected $errorHandler;

    public function __construct()
    {
        ini_set('display_errors', '1');
        set_time_limit(0);
    }

    /**
     * Configure the server/router
     *
     * @param array $arguments
     */
    abstract protected function configure(array $arguments);

    /**
     * Executes the routing or starts the server
     *
     * @param array $arguments
     * @return
     */
    abstract protected function doExecute(array $arguments);

    /**
     * Start the server
     *
     * @param array $arguments
     */
    public function execute(array $arguments)
    {
        $this->configure($arguments);
        $this->configureErrorHandling();

        $this->doExecute($arguments);

        $this->crashHandler->unregister();
    }

    /**
     * Configure the error handling
     */
    protected function configureErrorHandling()
    {
        $this->crashHandler = new CrashHandler();
        $this->crashHandler->register();

        $this->errorHandler = new ErrorHandler();
        $this->errorHandler->register();
    }

    /**
     * Configure the data path
     *
     * @param string $dataPath
     * @return AbstractBootstrap
     */
    protected function setDataPath($dataPath): AbstractBootstrap
    {
        if (!$dataPath) {
            return $this;
        }

        if (substr($dataPath, -1) !== '/') {
            return $this->setDataPath($dataPath . '/');
        }

        $configurationManager = ConfigurationManager::getSharedInstance();
        $configurationManager->setConfigurationForKeyPath('dataPath', (string)$dataPath);
        $configurationManager->setConfigurationForKeyPath('writeDataPath', (string)$dataPath);

        return $this;
    }
}