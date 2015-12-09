<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 02.02.15
 * Time: 19:43
 */

namespace Cundd\PersistentObjectStore\Bootstrap;

use Cundd\PersistentObjectStore\Configuration\ConfigurationManager;
use Cundd\PersistentObjectStore\Server\ServerInterface;
use Cundd\PersistentObjectStore\Server\ValueObject\SimpleResponse;
use DI\Container;
use Psr\Log\LogLevel;
use React\Http\Request;
use React\Http\Response;

/**
 * Router bootstrapping for conventional servers
 *
 * @package Cundd\PersistentObjectStore\Bootstrap
 */
class Router extends AbstractBootstrap
{
    /**
     * Current response instance
     *
     * @var Response
     */
    protected $response;

    /**
     * Current request's arguments
     *
     * @var array
     */
    protected $arguments = array();

    /**
     * Configure the server/router
     *
     * @param array $arguments
     * @throws \DI\NotFoundException
     */
    public function configure($arguments)
    {
        $this->arguments = $arguments;
        $dataPath        = getenv('STAIRTOWER_SERVER_DATA_PATH');
        $serverMode      = $this->getServerModeFromEnv();

        $configurationManager = ConfigurationManager::getSharedInstance();
        if ($serverMode === ServerInterface::SERVER_MODE_DEVELOPMENT) {
            $configurationManager->setConfigurationForKeyPath('logLevel', LogLevel::DEBUG);
        }

        if ($dataPath) {
            $configurationManager->setConfigurationForKeyPath('dataPath', $dataPath);
            $configurationManager->setConfigurationForKeyPath('writeDataPath', $dataPath);
        }

        // Instantiate the Core
        $bootstrap = new Core();

        /** @var Container $diContainer */
        $diContainer = $bootstrap->getDiContainer();

        $this->server = $diContainer->get('Cundd\\PersistentObjectStore\\Server\\RestServer');
        $diContainer->set('Cundd\\PersistentObjectStore\\Server\\ServerInterface', $this->server);

        // Set the server mode
        $this->server->setMode($serverMode);
    }

    /**
     * Executes the routing or starts the server
     */
    protected function doExecute()
    {
        $request  = $this->createRequestInstance();
        $response = $this->getResponse();
        $this->server->prepareAndHandle($request, $response);
        $this->handleSentData($request);
    }

    /**
     * Builds the request instance from the current request arguments
     *
     * @return Request
     */
    protected function createRequestInstance()
    {
        $mergedArguments = array_reduce($this->arguments, function ($carry, $item) {
            return array_merge($carry, array_change_key_case($item, CASE_LOWER));
        }, array());

        $requestPath = parse_url($mergedArguments['request_uri'], PHP_URL_PATH);

        $request = new Request(
            $mergedArguments['request_method'],
            $requestPath,
            $this->arguments['get'],
            '1.1',
            $this->getAllHeaders()
        );

        return $request;
    }

    /**
     * Builds a response instance for the current request
     *
     * @return SimpleResponse
     */
    public function getResponse()
    {
        if (!$this->response) {
            $this->response = new SimpleResponse();
        }

        return $this->response;
    }

    /**
     * Returns the headers for the current request arguments
     *
     * @return array
     */
    protected function getAllHeaders()
    {
        if (function_exists('getallheaders')) {
            return getallheaders();
        }

        $headers = array();
        foreach ($this->arguments['server'] as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        return $headers;
    }

    /**
     * Returns the server mode according to the environment variables
     *
     * @return int
     */
    protected function getServerModeFromEnv()
    {
        $serverModeName = getenv('STAIRTOWER_SERVER_MODE');
        if ($serverModeName === 'dev') {
            return ServerInterface::SERVER_MODE_DEVELOPMENT;
        } elseif ($serverModeName === 'dev') {
            return ServerInterface::SERVER_MODE_TEST;
        }

        return ServerInterface::SERVER_MODE_NORMAL;
    }

    /**
     * Handle sent data
     *
     * @param Request $request
     */
    private function handleSentData($request)
    {
        $rawPostData = file_get_contents('php://input');
        if ($rawPostData) {
            $request->emit('data', [$rawPostData]);
            $this->server->runMaintenance();
        }
    }
}
