<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server;


use Cundd\Stairtower\Server\ValueObject\Statistics;
use DateTimeInterface;
use React\EventLoop\LoopInterface;

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

//    /**
//     * Handle the given request
//     *
//     * @param RequestInterface  $request
//     * @param ResponseInterface $response
//     */
//    public function handle(RequestInterface $request, ResponseInterface $response): void;

//    /**
//     * Handle the given request result
//     *
//     * @param HandlerResultInterface $result
//     * @param RequestInterface       $request
//     * @param ResponseInterface      $response
//     */
//    public function handleResult(
//        HandlerResultInterface $result,
//        RequestInterface $request,
//        ResponseInterface $response
//    ): void;


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
     * @return DateTimeInterface|null
     */
    public function getStartTime(): ?DateTimeInterface;

    /**
     * Returns if the server is running
     *
     * @return bool
     */
    public function isRunning(): bool;

    /**
     * Collects and returns the current server statistics
     *
     * @return Statistics
     */
    public function collectStatistics(): Statistics;

//    /**
//     * Returns the formatter for the given request
//     *
//     * @param RequestInterface $request
//     * @return FormatterInterface
//     */
//    public function getFormatterForRequest(RequestInterface $request): FormatterInterface;

//    /**
//     * Returns the requested content type
//     *
//     * @param RequestInterface $request
//     * @return string
//     */
//    public function getContentTypeForRequest(RequestInterface $request): string;

//    /**
//     * Returns the body parser for the given request
//     *
//     * @param RequestInterface $request
//     * @return BodyParserInterface
//     */
//    public function getBodyParserForRequest(RequestInterface $request): BodyParserInterface;

    /**
     * Sets the IP to listen on
     *
     * @param string $ip
     * @return ServerInterface
     */
    public function setIp(string $ip): ServerInterface;

    /**
     * Returns the URI to listen on
     *
     * @return string
     */
    public function getUri(): string;

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
     * @return ServerInterface
     */
    public function setPort(int $port): ServerInterface;

    /**
     * Returns the port number to listen on
     *
     * @return int
     */
    public function getPort(): int;

    /**
     * Returns the event loop instance
     *
     * @return LoopInterface if the event loop is not set
     */
    public function getEventLoop(): LoopInterface;

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
