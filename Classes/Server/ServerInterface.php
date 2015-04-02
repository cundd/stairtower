<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 13.10.14
 * Time: 13:02
 */

namespace Cundd\PersistentObjectStore\Server;


use Cundd\PersistentObjectStore\Formatter\FormatterInterface;
use Cundd\PersistentObjectStore\Server\BodyParser\BodyParserInterface;
use Cundd\PersistentObjectStore\Server\Exception\InvalidEventLoopException;
use Cundd\PersistentObjectStore\Server\Handler\HandlerResultInterface;
use Cundd\PersistentObjectStore\Server\ValueObject\Request;
use Cundd\PersistentObjectStore\Server\ValueObject\Statistics;
use DateTime;
use Exception;
use React\Http\Response;

/**
 * Interface for server implementations
 *
 * @package Cundd\PersistentObjectStore\Server
 */
interface ServerInterface
{
    /**
     * Server is not running
     */
    const SERVER_MODE_NOT_RUNNING = -1;
    /**
     * Server is in normal operation mode
     */
    const SERVER_MODE_NORMAL = 0;

    /**
     * Server is in test mode
     */
    const SERVER_MODE_TEST = 1;

    /**
     * Server is in development mode
     */
    const SERVER_MODE_DEVELOPMENT = 2;

    /**
     * Handle the given request
     *
     * @param Request              $request
     * @param \React\Http\Response $response
     */
    public function handle($request, $response);

    /**
     * Handle the given request result
     *
     * @param HandlerResultInterface $result
     * @param Request                $request
     * @param Response               $response
     */
    public function handleResult($result, $request, $response);

    /**
     * Handles the given exception
     *
     * @param Exception            $error
     * @param Request              $request
     * @param \React\Http\Response $response
     * @throws Exception
     */
    public function handleError($error, $request, Response $response);


    /**
     * Starts the request loop
     */
    public function start();

    /**
     * Stops to listen for connections
     */
    public function stop();

    /**
     * Restarts the server
     */
    public function restart();

    /**
     * Total shutdown of the server
     *
     * Stops to listen for incoming connections, runs the maintenance task and terminates the program
     */
    public function shutdown();


    /**
     * Returns the servers global unique identifier
     *
     * @return string
     */
    public function getGuid();

    /**
     * Returns the servers start time
     *
     * @return DateTime
     */
    public function getStartTime();

    /**
     * Returns if the server is running
     *
     * @return bool
     */
    public function isRunning();

    /**
     * Collects and returns the current server statistics
     *
     * @param bool $detailed If detailed is TRUE more data will be collected and an array will be returned
     * @return Statistics|array
     */
    public function collectStatistics($detailed = false);

    /**
     * Returns the formatter for the given request
     *
     * @param Request $request
     * @return FormatterInterface
     */
    public function getFormatterForRequest(Request $request);

    /**
     * Returns the requested content type
     *
     * @param Request $request
     * @return string
     */
    public function getContentTypeForRequest(Request $request);

    /**
     * Returns the body parser for the given request
     *
     * @param Request $request
     * @return BodyParserInterface
     */
    public function getBodyParserForRequest(Request $request);


    /**
     * Sets the IP to listen on
     *
     * @param string $ip
     * @return $this
     */
    public function setIp($ip);

    /**
     * Returns the IP to listen on
     *
     * @return string
     */
    public function getIp();

    /**
     * Sets the port number to listen on
     *
     * @param int $port
     * @return $this
     */
    public function setPort($port);

    /**
     * Returns the port number to listen on
     *
     * @return int
     */
    public function getPort();

    /**
     * Returns the event loop instance
     *
     * @throws InvalidEventLoopException if the event loop is not set
     * @return \React\EventLoop\LoopInterface
     */
    public function getEventLoop();

    /**
     * Sets the event loop
     *
     * @param \React\EventLoop\LoopInterface $eventLoop
     * @return $this
     */
    public function setEventLoop($eventLoop);

    /**
     * Returns the mode of the server
     *
     * @return int
     */
    public function getMode();

    /**
     * Sets the mode of the server
     *
     * @param int $mode
     * @return $this
     */
    public function setMode($mode);

    /**
     * Returns the number of seconds after which to stop the server if run in test mode
     *
     * @return int
     */
    public function getAutoShutdownTime();

    /**
     * Sets the number of seconds after which to stop the server if run in test mode
     *
     * @param int $autoShutdownTime
     * @return $this
     */
    public function setAutoShutdownTime($autoShutdownTime);
}