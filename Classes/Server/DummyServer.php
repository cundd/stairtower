<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 15.08.14
 * Time: 19:38
 */

namespace Cundd\PersistentObjectStore\Server;

use Cundd\PersistentObjectStore\Constants;
use Cundd\PersistentObjectStore\Formatter\FormatterInterface;
use Cundd\PersistentObjectStore\Server\BodyParser\BodyParserInterface;
use Cundd\PersistentObjectStore\Server\Exception\InvalidEventLoopException;
use Cundd\PersistentObjectStore\Server\Exception\InvalidServerChangeException;
use Cundd\PersistentObjectStore\Server\Exception\ServerException;
use Cundd\PersistentObjectStore\Server\Handler\HandlerInterface;
use Cundd\PersistentObjectStore\Server\Handler\HandlerResultInterface;
use Cundd\PersistentObjectStore\Server\ValueObject\HandlerResult;
use Cundd\PersistentObjectStore\Server\ValueObject\Statistics;
use DateTime;
use React\Http\Request;
use React\Http\Response;

/**
 * A dummy server implementation for testing
 *
 * @package Cundd\PersistentObjectStore
 */
class DummyServer implements ServerInterface {
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
	protected $_isRunning = FALSE;

	/**
	 * Servers start time
	 *
	 * @var DateTime
	 */
	protected $startTime;

	/**
	 * Handles the given exception
	 *
	 * @param \Exception           $error
	 * @param \React\Http\Request $request
	 * @param \React\Http\Response $response
	 * @throws \Exception
	 */
	public function handleError($error, $request, Response $response) {
		throw $error;
	}

	/**
	 * Restarts the server
	 */
	public function restart() {
		if (!$this->isRunning()) throw new ServerException('Server is currently not running', 1413201286);
		$this->stop();
		$this->start();
	}

	/**
	 * Total shutdown of the server
	 *
	 * Stops to listen for incoming connections, runs the maintenance task and terminates the programm
	 */
	public function shutdown() {}

	/**
	 * Sets the IP to listen on
	 *
	 * @param string $ip
	 * @return $this
	 */
	public function setIp($ip) {
		if ($this->_isRunning) throw new InvalidServerChangeException('Can not change IP when server is running', 1412956590);
		$this->ip = $ip;
		return $this;
	}

	/**
	 * Returns the IP to listen on
	 *
	 * @return string
	 */
	public function getIp() {
		return $this->ip;
	}

	/**
	 * Sets the port number to listen on
	 * @param int $port
	 * @return $this
	 */
	public function setPort($port) {
		if ($this->_isRunning) throw new InvalidServerChangeException('Can not change port when server is running', 1412956591);
		$this->port = $port;
		return $this;
	}

	/**
	 * Returns the port number to listen on
	 * @return int
	 */
	public function getPort() {
		return $this->port;
	}

	/**
	 * Returns the event loop instance
	 *
	 * @throws InvalidEventLoopException if the event loop is not set
	 * @return \React\EventLoop\LoopInterface
	 */
	public function getEventLoop() {
		if (!$this->eventLoop) throw new InvalidEventLoopException('Event loop not set', 1412942824);
		return $this->eventLoop;
	}

	/**
	 * Sets the event loop
	 *
	 * @param \React\EventLoop\LoopInterface $eventLoop
	 * @return $this
	 */
	public function setEventLoop($eventLoop) {
		if ($this->_isRunning) throw new InvalidServerChangeException('Can not change the event loop when server is running', 1412956592);
		$this->eventLoop = $eventLoop;
		return $this;
	}

	/**
	 * Returns the mode of the server
	 *
	 * @return int
	 */
	public function getMode() {
	}

	/**
	 * Sets the mode of the server
	 *
	 * @param int $mode
	 * @return $this
	 */
	public function setMode($mode) {
	}

	/**
	 * Returns the number of seconds after which to stop the server if run in test mode
	 *
	 * @return int
	 */
	public function getAutoShutdownTime() {
	}

	/**
	 * Sets the number of seconds after which to stop the server if run in test mode
	 *
	 * @param int $autoShutdownTime
	 * @return $this
	 */
	public function setAutoShutdownTime($autoShutdownTime) {
	}


	/**
	 * Returns the servers global unique identifier
	 * @return string
	 */
	public function getGuid() {
		return sprintf('stairtower_%s_%s_%s_%d',
			Constants::VERSION,
			getmypid(),
			$this->getIp(),
			$this->getPort()
		);
	}

	/**
	 * Returns the servers start time
	 *
	 * @return DateTime
	 */
	public function getStartTime() {
		return $this->startTime;
	}

	/**
	 * Returns if the server is running
	 *
	 * @return bool
	 */
	public function isRunning() {
		return $this->_isRunning;
	}

	/**
	 * Collects and returns the current server statistics
	 *
	 * @param bool $detailed If detailed is TRUE more data will be collected and an array will be returned
	 * @return Statistics|array
	 */
	public function collectStatistics($detailed = FALSE) {
		return new Statistics(Constants::VERSION, $this->getGuid(), $this->getStartTime(), memory_get_usage(TRUE), memory_get_peak_usage(TRUE));
	}


	/**
	 * Handle the given request
	 *
	 * @param \React\Http\Request  $request
	 * @param \React\Http\Response $response
	 */
	public function handle($request, $response) {
	}

	/**
	 * Handle the given request result
	 *
	 * @param HandlerResultInterface $result
	 * @param Request                $request
	 * @param Response               $response
	 */
	public function handleResult($result, $request, $response) {
	}

	/**
	 * Returns the formatter for the given request
	 *
	 * @param Request $request
	 * @return FormatterInterface
	 */
	public function getFormatterForRequest(Request $request) {
		return $this->diContainer->get('Cundd\\PersistentObjectStore\\Formatter\\JsonFormatter');
	}

	/**
	 * Returns the handler for the given request
	 *
	 * @param Request $request
	 * @return HandlerInterface
	 */
	public function getHandlerForRequest(Request $request) {
		return $this->diContainer->get('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface');
	}

	/**
	 * Returns the body parser for the given request
	 *
	 * @param Request $request
	 * @return BodyParserInterface
	 */
	public function getBodyParserForRequest(Request $request) {
		return $this->diContainer->get('Cundd\\PersistentObjectStore\\Server\\BodyParser\\BodyParserInterface');
	}

	/**
	 * Create and configure the server objects
	 */
	protected function setupServer() {
		// Do nothing
	}

	/**
	 * Starts the request loop
	 */
	public function start() {
		$this->setupServer();
		$this->startTime = new DateTime();
		$this->_isRunning = TRUE;
	}

	/**
	 * Stops to listen for connections
	 */
	public function stop() {
		$this->_isRunning = FALSE;
	}

	/**
	 * Inform the client and restart the server
	 *
	 * @param \React\Http\Request  $request
	 * @param \React\Http\Response $response
	 */
	protected function restartWithParameters($request, $response) {
		if (!$this->isRunning()) throw new ServerException('Server is currently not running', 1413201286);
		$this->handleResult(new HandlerResult(200, 'Server is going to restart'), $request, $response);
		$this->restart();
	}

}