<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 13.10.14
 * Time: 13:02
 */

namespace Cundd\PersistentObjectStore\Server;


use Cundd\PersistentObjectStore\Formatter\FormatterInterface;
use Cundd\PersistentObjectStore\Server\Exception\InvalidEventLoopException;
use Cundd\PersistentObjectStore\Server\Handler\HandlerInterface;
use Cundd\PersistentObjectStore\Server\Handler\HandlerResultInterface;
use Cundd\PersistentObjectStore\Server\ValueObject\Statistics;
use DateTime;
use React\Http\Request;
use React\Http\Response;

interface ServerInterface {
	/**
	 * Handle the given request
	 *
	 * @param \React\Http\Request  $request
	 * @param \React\Http\Response $response
	 */
	public function handle($request, $response);

	/**
	 * Handle the given request result
	 *
	 * @param HandlerResultInterface $result
	 * @param Request $request
	 * @param Response $response
	 */
	public function handleResult($result, $request, $response);

	/**
	 * Handles the given exception
	 *
	 * @param \Exception           $error
	 * @param \React\Http\Response $response
	 * @throws \Exception
	 */
	public function handleError($error, \React\Http\Response $response);


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
	 * Returns the servers global unique identifier
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
	 * @return Statistics
	 */
	public function collectStatistics();

	/**
	 * Returns a promise for the request body of the given request
	 *
	 * @param Request $request
	 * @return \React\Promise\Promise
	 */
	public function getRequestBodyPromiseForRequest($request);

	/**
	 * Returns the formatter for the given request
	 *
	 * @param Request $request
	 * @return FormatterInterface
	 */
	public function getFormatterForRequest(Request $request);

	/**
	 * Returns the handler for the given request
	 *
	 * @param Request $request
	 * @return HandlerInterface
	 */
	public function getHandlerForRequest(Request $request);


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
	 * @param int $port
	 * @return $this
	 */
	public function setPort($port);

	/**
	 * Returns the port number to listen on
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
}