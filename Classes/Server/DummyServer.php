<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Server;

use Cundd\PersistentObjectStore\Constants;
use Cundd\PersistentObjectStore\Formatter\FormatterInterface;
use Cundd\PersistentObjectStore\Formatter\JsonFormatter;
use Cundd\PersistentObjectStore\Server\BodyParser\BodyParserInterface;
use Cundd\PersistentObjectStore\Server\Exception\InvalidEventLoopException;
use Cundd\PersistentObjectStore\Server\Exception\InvalidServerChangeException;
use Cundd\PersistentObjectStore\Server\Exception\ServerException;
use Cundd\PersistentObjectStore\Server\Handler\HandlerInterface;
use Cundd\PersistentObjectStore\Server\Handler\HandlerResultInterface;
use Cundd\PersistentObjectStore\Server\ValueObject\HandlerResult;
use Cundd\PersistentObjectStore\Server\ValueObject\Request;
use Cundd\PersistentObjectStore\Server\ValueObject\RequestInterface;
use Cundd\PersistentObjectStore\Server\ValueObject\Statistics;
use DateTime;
use DateTimeInterface;
use Exception;
use React\EventLoop\LoopInterface;
use React\Http\Response;
use React\Stream\WritableStreamInterface;

/**
 * A dummy server implementation for testing
 */
class DummyServer implements ServerInterface
{
    /**
     * Port number to listen on
     *
     * @var int
     */
    protected $port = 1338;

    /**
     * IP to listen on
     *
     * @var string
     */
    protected $ip = '127.0.0.1';

    /**
     * Document Access Coordinator
     *
     * @var \Cundd\PersistentObjectStore\DataAccess\CoordinatorInterface
     * @Inject
     */
    protected $coordinator;

    /**
     * JSON serializer
     *
     * @var \Cundd\PersistentObjectStore\Serializer\JsonSerializer
     * @Inject
     */
    protected $serializer;

    /**
     * DI container
     *
     * @var \DI\Container
     * @Inject
     */
    protected $diContainer;

    /**
     * Formatter
     *
     * @var \Cundd\PersistentObjectStore\Formatter\FormatterInterface
     */
    protected $formatter;

    /**
     * Event loop
     *
     * @var \React\EventLoop\LoopInterface
     */
    protected $eventLoop;

    /**
     * Indicates if the server is currently running
     *
     * @var bool
     */
    protected $_isRunning = false;

    /**
     * Servers start time
     *
     * @var DateTime
     */
    protected $startTime;

    public function __construct()
    {
        $this->startTime = new DateTime();
    }


    /**
     * Handles the given exception
     *
     * @param Exception                        $error
     * @param RequestInterface                 $request
     * @param WritableStreamInterface|Response $response
     * @throws Exception
     */
    public function handleError(Exception $error, RequestInterface $request, WritableStreamInterface $response): void
    {
        throw $error;
    }

    /**
     * Total shutdown of the server
     *
     * Stops to listen for incoming connections, runs the maintenance task and terminates the programm
     */
    public function shutdown()
    {
    }

    /**
     * Returns the event loop instance
     *
     * @throws InvalidEventLoopException if the event loop is not set
     * @return \React\EventLoop\LoopInterface
     */
    public function getEventLoop()
    {
        if (!$this->eventLoop) {
            throw new InvalidEventLoopException('Event loop not set', 1412942824);
        }

        return $this->eventLoop;
    }

    public function setEventLoop(LoopInterface $eventLoop): ServerInterface
    {
        if ($this->_isRunning) {
            throw new InvalidServerChangeException('Can not change the event loop when server is running', 1412956592);
        }
        $this->eventLoop = $eventLoop;

        return $this;
    }

    /**
     * Returns the mode of the server
     *
     * @return int
     */
    public function getMode(): int
    {
        return ServerInterface::SERVER_MODE_TEST;
    }

    /**
     * Sets the mode of the server
     *
     * @param int $mode
     * @return ServerInterface
     */
    public function setMode(int $mode): ServerInterface
    {
        return $this;
    }

    /**
     * Returns the number of seconds after which to stop the server if run in test mode
     *
     * @return int
     */
    public function getAutoShutdownTime(): int
    {
        return 0;
    }

    /**
     * Sets the number of seconds after which to stop the server if run in test mode
     *
     * @param int $autoShutdownTime
     * @return ServerInterface
     */
    public function setAutoShutdownTime(int $autoShutdownTime): ServerInterface
    {
        return $this;
    }

    /**
     * Collects and returns the current server statistics
     *
     * @param bool $detailed If detailed is TRUE more data will be collected and an array will be returned
     * @return array|Statistics
     */
    public function collectStatistics(bool $detailed = false)
    {
        return new Statistics(
            Constants::VERSION, $this->getGuid(), $this->getStartTime(), memory_get_usage(true),
            memory_get_peak_usage(true)
        );
    }

    /**
     * Returns the servers global unique identifier
     *
     * @return string
     */
    public function getGuid()
    {
        return sprintf(
            'stairtower_%s_%s_%s_%d',
            Constants::VERSION,
            getmypid(),
            $this->getIp(),
            $this->getPort()
        );
    }

    /**
     * Returns the IP to listen on
     *
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * Sets the IP to listen on
     *
     * @param string $ip
     * @return $this
     */
    public function setIp($ip)
    {
        if ($this->_isRunning) {
            throw new InvalidServerChangeException('Can not change IP when server is running', 1412956590);
        }
        $this->ip = $ip;

        return $this;
    }

    /**
     * Returns the port number to listen on
     *
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * Sets the port number to listen on
     *
     * @param int $port
     * @return $this
     */
    public function setPort(int $port)
    {
        if ($this->_isRunning) {
            throw new InvalidServerChangeException('Can not change port when server is running', 1412956591);
        }
        $this->port = $port;

        return $this;
    }

    /**
     * Returns the servers start time
     *
     * @return DateTimeInterface
     */
    public function getStartTime(): DateTimeInterface
    {
        return $this->startTime;
    }

    /**
     * Handle the given request
     *
     * @param RequestInterface|\React\Http\Request $request
     * @param Response|WritableStreamInterface     $response
     */
    public function handle(RequestInterface $request, WritableStreamInterface $response): void
    {
    }

    /**
     * Returns the formatter for the given request
     *
     * @param RequestInterface|Request $request
     * @return FormatterInterface
     */
    public function getFormatterForRequest(RequestInterface $request): FormatterInterface
    {
        return $this->diContainer->get(JsonFormatter::class);
    }

    /**
     * Returns the handler for the given request
     *
     * @param RequestInterface $request
     * @return HandlerInterface
     */
    public function getHandlerForRequest(RequestInterface $request)
    {
        return $this->diContainer->get(HandlerInterface::class);
    }

    /**
     * Returns the requested content type
     *
     * @param RequestInterface|Request $request
     * @return string
     */
    public function getContentTypeForRequest(RequestInterface $request): string
    {
        return ContentType::JSON_APPLICATION;
    }

    /**
     * Returns the body parser for the given request
     *
     * @param RequestInterface|Request $request
     * @return BodyParserInterface
     */
    public function getBodyParserForRequest(RequestInterface $request): BodyParserInterface
    {
        return $this->diContainer->get(BodyParserInterface::class);
    }

    /**
     * Inform the client and restart the server
     *
     * @param \React\Http\Request|RequestInterface $request
     * @param \React\Http\Response                 $response
     */
    protected function restartWithParameters($request, $response)
    {
        if (!$this->isRunning()) {
            throw new ServerException('Server is currently not running', 1413201286);
        }
        $this->handleResult(new HandlerResult(200, 'Server is going to restart'), $request, $response);
        $this->restart();
    }

    /**
     * Returns if the server is running
     *
     * @return bool
     */
    public function isRunning(): bool
    {
        return $this->_isRunning;
    }

    /**
     * Handle the given request result
     *
     * @param HandlerResultInterface           $result
     * @param RequestInterface|Request         $request
     * @param Response|WritableStreamInterface $response
     */
    public function handleResult(
        HandlerResultInterface $result,
        RequestInterface $request,
        WritableStreamInterface $response
    ): void {
    }

    /**
     * Restarts the server
     */
    public function restart()
    {
        if (!$this->isRunning()) {
            throw new ServerException('Server is currently not running', 1413201286);
        }
        $this->stop();
        $this->start();
    }

    /**
     * Stops to listen for connections
     */
    public function stop()
    {
        $this->_isRunning = false;
    }

    /**
     * Starts the request loop
     */
    public function start()
    {
        $this->setupServer();
        $this->startTime = new DateTime();
        $this->_isRunning = true;
    }

    /**
     * Create and configure the server objects
     */
    protected function setupServer()
    {
        // Do nothing
    }
}
