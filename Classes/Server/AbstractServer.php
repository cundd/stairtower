<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 15.08.14
 * Time: 19:38
 */

namespace Cundd\PersistentObjectStore\Server;
use Cundd\PersistentObjectStore\Server\Exception\InvalidEventLoopException;
use Cundd\PersistentObjectStore\Server\Exception\InvalidServerChangeException;

/**
 * Abstract server
 *
 * @package Cundd\PersistentObjectStore
 */
abstract class AbstractServer {
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
	protected $ip = '0.0.0.0';

	/**
	 * Data Access Coordinator
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
	 * @Inject
	 */
	protected $eventLoop;

	/**
	 * Indicates if the server is currently running
	 *
	 * @var bool
	 */
	protected $isRunning = FALSE;

	/**
	 * Create and configure the server objects
	 */
	abstract protected function setupServer();

	/**
	 * Starts the request loop
	 */
	public function start() {
		$this->setupServer();
		$this->isRunning = TRUE;
		$this->eventLoop->run();
		$this->isRunning = FALSE;
	}

	/**
	 * Stops to listen for connections
	 */
	public function stop() {
		$this->getEventLoop()->stop();
		$this->isRunning = FALSE;
	}

	/**
	 * Sets the IP to listen on
	 *
	 * @param string $ip
	 * @return $this
	 */
	public function setIp($ip) {
		if ($this->isRunning) throw new InvalidServerChangeException('Can not change IP when server is running', 1412956590);
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
		if ($this->isRunning) throw new InvalidServerChangeException('Can not change port when server is running', 1412956591);
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
		if ($this->isRunning) throw new InvalidServerChangeException('Can not change the event loop when server is running', 1412956592);
		$this->eventLoop = $eventLoop;
		return $this;
	}

	/**
	 * Handles the given exception
	 *
	 * @param \Exception           $error
	 * @param \React\Http\Response $response
	 * @throws \Exception
	 */
	protected function handleError($error, \React\Http\Response $response) {
		throw $error;
	}

	/**
	 * Outputs the given value for information
	 *
	 * @param string $format
	 * @param mixed $vars…
	 */
	protected function write($format, $vars = NULL) {
		if (func_num_args() > 1) {
			$arguments = func_get_args();
			array_shift($arguments);
			fwrite(STDOUT, vsprintf($format, $arguments));
		} else {
			fwrite(STDOUT, $format);
		}
	}

	/**
	 * Outputs the given value for information
	 *
	 * @param string $format
	 * @param mixed $vars…
	 */
	protected function writeln($format, $vars = NULL) {
		$arguments = func_get_args();
		call_user_func_array(array($this, 'write'), $arguments);

		$this->write(PHP_EOL);
	}
}