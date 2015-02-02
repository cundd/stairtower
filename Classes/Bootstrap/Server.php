<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 02.02.15
 * Time: 19:43
 */

namespace Cundd\PersistentObjectStore\Bootstrap;

use Cundd\PersistentObjectStore\Configuration\ConfigurationManager;
use Cundd\PersistentObjectStore\Constants;
use Cundd\PersistentObjectStore\CrashHandler;
use Cundd\PersistentObjectStore\Server\ServerInterface;
use DI\Container;
use Psr\Log\LogLevel;
use React\EventLoop\Factory;

/**
 * Server bootstrapping
 *
 * @package Cundd\PersistentObjectStore\Bootstrap
 */
class Server
{
    /**
     * Server instance
     *
     * @var \Cundd\PersistentObjectStore\Server\RestServer
     */
    protected $server;

    /**
     * @param array $arguments
     */
    public function __construct($arguments)
    {
        ini_set('display_errors', true);

        (new CrashHandler())->register();

        // Parse the arguments
        $longOptions = array(
            "port::",
            "ip::",
            "data-path::",
            'test::',
            'dev::',
        );
        $options     = getopt('h::', $longOptions);

        // Print the help
        if (isset($options['h'])) {
            print(Constants::MESSAGE_CLI_WELCOME . PHP_EOL);
            printf('Usage: %s [--port=port] [--ip=ip] [--data-path=path/to/data/folder/] [--dev]' . PHP_EOL,
                $arguments[0]);
            exit;
        }

        $dataPath   = $this->checkArgument('data-path', $options);
        $port       = $this->checkArgument('port', $options);
        $ip         = $this->checkArgument('ip', $options);
        $serverMode = $this->getServerMode($options);

        $configurationManager = ConfigurationManager::getSharedInstance();
        if ($serverMode === ServerInterface::SERVER_MODE_DEVELOPMENT) {
            $configurationManager->setConfigurationForKeyPath('logLevel', LogLevel::DEBUG);
        }

        if ($dataPath) {
            $configurationManager->setConfigurationForKeyPath('dataPath', $dataPath);
            $configurationManager->setConfigurationForKeyPath('writeDataPath', $dataPath);
        }

        $bootstrap = new Core();

        /** @var Container $diContainer */
        $diContainer = $bootstrap->getDiContainer();

        $this->server = $diContainer->get('Cundd\\PersistentObjectStore\\Server\\RestServer');
        $diContainer->set('Cundd\\PersistentObjectStore\\Server\\ServerInterface', $this->server);

        if ($ip) {
            $this->server->setIp($ip);
        }
        if ($port) {
            $this->server->setPort($port);
        }

        // Set the server mode
        $this->server->setMode($serverMode);
        if ($serverMode === ServerInterface::SERVER_MODE_TEST && is_numeric($options['test'])) {
            $this->server->setAutoShutdownTime(intval($options['test']));
        }
    }

    /**
     * Start the server
     */
    public function startServer()
    {
        $this->server->start();
    }

    /**
     * Checks and returns an argument value
     *
     * @param $key
     * @param $options
     * @return null
     */
    protected function checkArgument($key, $options)
    {
        if (isset($options[$key])) {
            if ($options[$key] === false) {
                printf('The option --%s requires a value' . PHP_EOL, $key);
                exit(1);
            }
            return $options[$key];
        }
        return null;
    }

    /**
     * Returns the server mode according to the options
     *
     * @param $options
     * @return int
     */
    protected function getServerMode($options)
    {
        if (isset($options['dev'])) {
            return ServerInterface::SERVER_MODE_DEVELOPMENT;
        } elseif (isset($options['test'])) {
            return ServerInterface::SERVER_MODE_TEST;
        }
        return ServerInterface::SERVER_MODE_NORMAL;
    }
}