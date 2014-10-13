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
use Cundd\PersistentObjectStore\Server\Exception\InvalidRequestMethodServerException;
use Cundd\PersistentObjectStore\Server\Exception\ServerException;
use Cundd\PersistentObjectStore\Server\Handler\HandlerInterface;
use Cundd\PersistentObjectStore\Server\Handler\HandlerResultInterface;
use Cundd\PersistentObjectStore\Server\ValueObject\HandlerResult;
use Cundd\PersistentObjectStore\Server\ValueObject\RequestInfoFactory;
use Cundd\PersistentObjectStore\Utility\ContentTypeUtility;
use React\Http\Server as HttpServer;
use React\Http\Request;
use React\Http\Response;
use React\Socket\Server as SocketServer;
use React\Stream\BufferedSink;

/**
 * REST server
 *
 * @package Cundd\PersistentObjectStore
 */
class RestServer extends AbstractServer {
	/**
	 * Port number to listen on
	 *
	 * @var int
	 */
	protected $port = 1338;

	/**
	 * Listening socket server
	 *
	 * @var SocketServer
	 */
	protected $socketServer;

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


			$handler     = $this->getHandlerForRequest($request);
			$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest($request);
			$specialHandlerAction = RequestInfoFactory::getHandlerActionForRequest($request);

			$requestResult = FALSE;

			if ($specialHandlerAction) { // Handle a special handler action
				$requestResult = call_user_func(array($handler, $specialHandlerAction), $requestInfo);
			} else { // Run normal methods
				$method = $request->getMethod();

				switch ($method) {
					case 'POST':
					case 'PUT':
						$promise = $this->getRequestBodyPromiseForRequest($request);
						$self    = $this;
						$promise->then(function ($body) use ($self, $handler, $request, $response, $requestInfo) {
							if ($request->getMethod() === 'POST') {
								$requestResult = $handler->create($requestInfo, $body);
							} else {
								$requestResult = $handler->update($requestInfo, $body);
							}
							$self->handleResult($requestResult, $request, $response);
						});
						break;

					case 'GET':
						$requestResult = $handler->read($requestInfo);
						break;

					case 'DELETE':
						$requestResult = $handler->delete($requestInfo);
						break;

					default:
						$requestResult = new HandlerResult(405, new InvalidRequestMethodServerException(sprintf('Request method "%s" not valid', $method)), 1413033763);
				}
			}
			$this->handleResult($requestResult, $request, $response);
		} catch (\Exception $exception) {
			$this->handleError($exception, $response);
		}
	}

	/**
	 * Handle the given request result
	 *
	 * @param HandlerResultInterface $result
	 * @param Request $request
	 * @param Response $response
	 */
	public function handleResult($result, $request, $response) {
		$formatter = $this->getFormatterForRequest($request);
		if ($result instanceof HandlerResultInterface) {
			$response->writeHead(
				$result->getStatusCode(),
				array('Content-Type' => ContentTypeUtility::convertSuffixToContentType($formatter->getContentSuffix()))
			);
			$response->end($formatter->format($result->getData()));
		} else if($result === NULL) {
			$response->writeHead(
				204,
				array('Content-Type' => ContentTypeUtility::convertSuffixToContentType($formatter->getContentSuffix()))
			);
			$response->end($formatter->format('No content'));
		} else {
			throw new \UnexpectedValueException('Handler result is of type ' . gettype($request), 1413210970);
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
	 * Create and configure the server objects
	 */
	protected function setupServer() {
		$this->socketServer = new SocketServer($this->getEventLoop());
		$httpServer   = new HttpServer($this->socketServer, $this->getEventLoop());
		$httpServer->on('request', array($this, 'handle'));
		$this->socketServer->listen($this->port, $this->ip);

		$this->writeln(Constants::MESSAGE_WELCOME . PHP_EOL);
		$this->writeln('Start listening on %s:%s', $this->ip, $this->port);
	}

	public function stop() {
		$this->socketServer->shutdown();
		parent::stop();
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