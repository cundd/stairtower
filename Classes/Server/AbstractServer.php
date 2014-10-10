<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 15.08.14
 * Time: 19:38
 */

namespace Cundd\PersistentObjectStore\Server;
use Cundd\PersistentObjectStore\DataAccess\Coordinator;
use Cundd\PersistentObjectStore\Domain\Model\Data;
use Cundd\PersistentObjectStore\Driver\Connection;
use Cundd\PersistentObjectStore\Driver\Driver;
use Cundd\PersistentObjectStore\Serializer\JsonSerializer;
use Cundd\PersistentObjectStore\Server\Exception\InvalidEventLoopException;
use Doctrine\DBAL\DriverManager;

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
	 * Formatter
	 *
	 * @var \Cundd\PersistentObjectStore\Formatter\JsonFormatter
	 * @Inject
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
	 * Sets the IP to listen on
	 *
	 * @param string $ip
	 * @return $this
	 */
	public function setIp($ip) {
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
		$this->eventLoop = $eventLoop;
		return $this;
	}

	/**
	 * Stops to listen for connections
	 */
	public function stop() {
		$this->getEventLoop()->stop();
	}

	/**
	 * Handles the given exception
	 *
	 * @param \Exception $error
	 */
	protected function handleError($error) {
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

	/**
	 * Starts to listen for connections
	 */
	abstract public function start();
}