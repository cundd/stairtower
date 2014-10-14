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
use Cundd\PersistentObjectStore\Server\AbstractServer;
use Cundd\PersistentObjectStore\Server\BodyParser\BodyParserInterface;
use Cundd\PersistentObjectStore\Server\Exception\InvalidEventLoopException;
use Cundd\PersistentObjectStore\Server\Exception\InvalidRequestMethodException;
use Cundd\PersistentObjectStore\Server\Exception\InvalidServerChangeException;
use Cundd\PersistentObjectStore\Server\Exception\ServerException;
use Cundd\PersistentObjectStore\Server\Handler\HandlerInterface;
use Cundd\PersistentObjectStore\Server\Handler\HandlerResultInterface;
use Cundd\PersistentObjectStore\Server\ValueObject\HandlerResult;
use Cundd\PersistentObjectStore\Server\ValueObject\RequestInfo;
use Cundd\PersistentObjectStore\Server\ValueObject\RequestInfoFactory;
use Cundd\PersistentObjectStore\Server\ValueObject\Statistics;
use Cundd\PersistentObjectStore\Utility\ContentTypeUtility;
use DateTime;
use React\Http\Server as HttpServer;
use React\Http\Request;
use React\Http\Response;
use React\Socket\Server as SocketServer;
use React\Stream\BufferedSink;

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
	 * @param \React\Http\Response $response
	 * @throws \Exception
	 */
	public function handleError($error, \React\Http\Response $response) {
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
		try {
			$serverAction = RequestInfoFactory::getServerActionForRequest($request);
			if ($serverAction) { // Handle a very special server action

				if ($serverAction === 'restart') {
					$this->restartWithParameters($request, $response);
					return;
				}

			}


			$delayedRequest       = FALSE;
			$handler              = $this->getHandlerForRequest($request);
			$requestInfo          = RequestInfoFactory::buildRequestInfoFromRequest($request);
			$specialHandlerAction = RequestInfoFactory::getHandlerActionForRequest($request);

			$requestResult = FALSE;

			if ($specialHandlerAction) { // Handle a special handler action
				$requestResult = call_user_func(array($handler, $specialHandlerAction), $requestInfo);
			} else if (!$requestInfo->getDatabaseIdentifier()) { // Show the welcome message
				$requestResult = $handler->noRoute($requestInfo);
			} else { // Run normal methods
				$method = $request->getMethod();

				switch ($method) {
					case 'POST':
					case 'PUT':
						$delayedRequest = TRUE;
					$this->waitForBodyAndPerformAction($request, $response, $requestInfo);


//					$promise = $this->getRequestBodyPromiseForRequest($request);
//						$promise->then(function ($body) use ($self, $handler, $request, $response, $requestInfo) {
//							$this->writeln('Body');
//							$this->writeln($body);
//							$data = $this->getBodyParserForRequest($request)->parse($body, $request);
//							if ($request->getMethod() === 'POST') {
//								$requestResult = $handler->create($requestInfo, $data);
//							} else {
//								$requestResult = $handler->update($requestInfo, $data);
//							}
//							$self->handleResult($requestResult, $request, $response);
//						});
						break;

					case 'GET':
						$requestResult = $handler->read($requestInfo);
						break;

					case 'DELETE':
						$requestResult = $handler->delete($requestInfo);
						break;

					default:
						$requestResult = new HandlerResult(405, new InvalidRequestMethodException(sprintf('Request method "%s" not valid', $method)), 1413033763);
				}
			}
			if (!$delayedRequest) {
				$this->handleResult($requestResult, $request, $response);
			}
		} catch (\Exception $exception) {
			$this->handleError($exception, $response);
		}
	}

	/**
	 * Handle the given request result
	 *
	 * @param HandlerResultInterface $result
	 * @param Request                $request
	 * @param Response               $response
	 */
	public function handleResult($result, $request, $response) {
		$formatter = $this->getFormatterForRequest($request);
		if ($result instanceof HandlerResultInterface) {
			$response->writeHead(
				$result->getStatusCode(),
				array('Content-Type' => ContentTypeUtility::convertSuffixToContentType($formatter->getContentSuffix()))
			);
			$responseData = $result->getData();
			if ($responseData) {
				$response->end($formatter->format($result->getData()));
			} else {
				$response->end();
			}

		} else if ($result === NULL) {
			$response->writeHead(
				204,
				array('Content-Type' => ContentTypeUtility::convertSuffixToContentType($formatter->getContentSuffix()))
			);
			$response->end($formatter->format('No content'));
		} else {
			throw new \UnexpectedValueException('Handler result is of type ' . gettype($result), 1413210970);
		}
	}

	/**
	 * Returns a promise for the request body of the given request
	 *
	 * @param Request $request
	 * @return \React\Promise\Promise
	 */
	public function getRequestBodyPromiseForRequest($request) {
		return BufferedSink::createPromise($request);
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

//	/**
//	 * Waits for the total request body and performs the needed action
//	 *
//	 * @param Request $request
//	 * @param Response $response
//	 * @param RequestInfo $requestInfo
//	 */
//	public function waitForBodyAndPerformAction($request, $response, $requestInfo) {
//		$self = $this;
//
//		$requestBody   = '';
//		$headers       = $request->getHeaders();
//		$contentLength = (int)$headers['Content-Length'];
//		$receivedData  = 0;
//		$request->on('data', function ($data) use ($self, $request, $response, &$requestBody, &$receivedData, $contentLength, $requestInfo) {
//			$requestBody .= $data;
//			$receivedData += strlen($data);
//			if ($receivedData >= $contentLength) {
//				$requestBodyParsed = $self->getBodyParserForRequest($request)->parse($requestBody, $request);
//				if ($request->getMethod() === 'POST') {
//					$requestResult = $self->getHandlerForRequest($request)->create($requestInfo, $requestBodyParsed);
//				} else {
//					$requestResult = $self->getHandlerForRequest($request)->update($requestInfo, $requestBodyParsed);
//				}
//				$self->handleResult($requestResult, $request, $response);
//			}
//		});
//	}

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