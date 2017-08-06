<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Server;


use Cundd\PersistentObjectStore\Formatter\FormatterInterface;
use Cundd\PersistentObjectStore\Server\BodyParser\BodyParserInterface;
use Cundd\PersistentObjectStore\Server\Exception\InvalidEventLoopException;
use Cundd\PersistentObjectStore\Server\Handler\HandlerResultInterface;
use Cundd\PersistentObjectStore\Server\ValueObject\RequestInterface;
use Cundd\PersistentObjectStore\Server\ValueObject\Statistics;
use DateTimeInterface;
use Exception;
use React\EventLoop\LoopInterface;
use React\Stream\WritableStreamInterface;

/**
 * Interface for server implementations
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
     * @param RequestInterface        $request
     * @param WritableStreamInterface $response
     */
    public function handle(RequestInterface $request, WritableStreamInterface $response): void;

    /**
     * Handle the given request result
     *
     * @param HandlerResultInterface  $result
     * @param RequestInterface        $request
     * @param WritableStreamInterface $response
     */
    public function handleResult(
        HandlerResultInterface $result,
        RequestInterface $request,
        WritableStreamInterface $response
    ): void;

    /**
     * Handles the given exception
     *
     * @param Exception               $error
     * @param RequestInterface        $request
     * @param WritableStreamInterface $response
     */
    public function handleError(
        Exception $error,
        RequestInterface $request,
        WritableStreamInterface $response
    ): void;

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
     * @return DateTimeInterface
     */
    public function getStartTime(): DateTimeInterface;

    /**
     * Returns if the server is running
     *
     * @return bool
     */
    public function isRunning(): bool;

    /**
     * Collects and returns the current server statistics
     *
     * @param bool $detailed If detailed is TRUE more data will be collected and an array will be returned
     * @return array|Statistics
     */
    public function collectStatistics(bool $detailed = false);

    /**
     * Returns the formatter for the given request
     *
     * @param RequestInterface $request
     * @return FormatterInterface
     */
    public function getFormatterForRequest(RequestInterface $request): FormatterInterface;

    /**
     * Returns the requested content type
     *
     * @param RequestInterface $request
     * @return string
     */
    public function getContentTypeForRequest(RequestInterface $request): string;

    /**
     * Returns the body parser for the given request
     *
     * @param RequestInterface $request
     * @return BodyParserInterface
     */
    public function getBodyParserForRequest(RequestInterface $request): BodyParserInterface;


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
    public function getIp(): string;

    /**
     * Sets the port number to listen on
     *
     * @param int $port
     * @return $this
     */
    public function setPort(int $port);

    /**
     * Returns the port number to listen on
     *
     * @return int
     */
    public function getPort(): int;

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
     * @param LoopInterface|\React\EventLoop\LoopInterface $eventLoop
     * @return ServerInterface
     */
    public function setEventLoop(LoopInterface $eventLoop): ServerInterface;

    /**
     * Returns the mode of the server
     *
     * @return int
     */
    public function getMode(): int;

    /**
     * Sets the mode of the server
     *
     * @param int $mode
     * @return ServerInterface
     */
    public function setMode(int $mode): ServerInterface;

    /**
     * Returns the number of seconds after which to stop the server if run in test mode
     *
     * @return int
     */
    public function getAutoShutdownTime(): int;

    /**
     * Sets the number of seconds after which to stop the server if run in test mode
     *
     * @param int $autoShutdownTime
     * @return ServerInterface
     */
    public function setAutoShutdownTime(int $autoShutdownTime): ServerInterface;
}