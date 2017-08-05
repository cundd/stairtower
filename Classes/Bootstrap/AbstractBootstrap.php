<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Bootstrap;

use Cundd\PersistentObjectStore\ErrorHandling\CrashHandler;
use Cundd\PersistentObjectStore\ErrorHandling\ErrorHandler;

/**
 * Abstract bootstrapping class for the server and router
 */
abstract class AbstractBootstrap
{
    /**
     * Server instance
     *
     * @var \Cundd\PersistentObjectStore\Server\RestServer
     */
    protected $server;

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

    /**
     * @param array $arguments
     */
    public function __construct($arguments)
    {
        ini_set('display_errors', '1');
        set_time_limit(0);

        $this->configure($arguments);
    }

    /**
     * Configure the server/router
     *
     * @param array $arguments
     * @throws \DI\NotFoundException
     */
    abstract public function configure($arguments);

    /**
     * Executes the routing or starts the server
     */
    abstract protected function doExecute();

    /**
     * Start the server
     */
    public function execute()
    {
        $this->configureErrorHandling();
        try {
            $this->doExecute();
        } catch (\Error $error) {
            $this->errorHandler->handle(
                $error->getCode(),
                $error->getMessage(),
                $error->getFile(),
                $error->getLine(),
                [$error]
            );
        }
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
}