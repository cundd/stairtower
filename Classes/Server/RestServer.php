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
use Cundd\PersistentObjectStore\Server\Exception\InvalidRequestMethodException;
use Cundd\PersistentObjectStore\Server\Exception\ServerException;
use Cundd\PersistentObjectStore\Server\Handler\HandlerInterface;
use Cundd\PersistentObjectStore\Server\Handler\HandlerResultInterface;
use Cundd\PersistentObjectStore\Server\ValueObject\HandlerResult;
use Cundd\PersistentObjectStore\Server\ValueObject\RequestInfo;
use Cundd\PersistentObjectStore\Server\ValueObject\RequestInfoFactory;
use Cundd\PersistentObjectStore\Utility\ContentTypeUtility;
use Cundd\PersistentObjectStore\Utility\DebugUtility;
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


			$delayedRequest       = FALSE;
			$handler              = $this->getHandlerForRequest($request);
			$requestInfo          = RequestInfoFactory::buildRequestInfoFromRequest($request);
			DebugUtility::var_dump($request->getQuery(), $requestInfo->getRequest()->getQuery());
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

	/**
	 * Waits for the total request body and performs the needed action
	 *
	 * @param Request $request
	 * @param Response $response
	 * @param RequestInfo $requestInfo
	 */
	public function waitForBodyAndPerformAction($request, $response, $requestInfo) {
		$self = $this;

		$requestBody   = '';
		$headers       = $request->getHeaders();
		$contentLength = (int)$headers['Content-Length'];
		$receivedData  = 0;
		$request->on('data', function ($data) use ($self, $request, $response, &$requestBody, &$receivedData, $contentLength, $requestInfo) {
			$requestBody .= $data;
			$receivedData += strlen($data);
			if ($receivedData >= $contentLength) {
				$requestBodyParsed = $self->getBodyParserForRequest($request)->parse($requestBody, $request);
				if ($request->getMethod() === 'POST') {
					$requestResult = $self->getHandlerForRequest($request)->create($requestInfo, $requestBodyParsed);
				} else {
					$requestResult = $self->getHandlerForRequest($request)->update($requestInfo, $requestBodyParsed);
				}
				$self->handleResult($requestResult, $request, $response);
			}
		});
	}

	/**
	 * Create and configure the server objects
	 */
	protected function setupServer() {
		$this->socketServer = new SocketServer($this->getEventLoop());

//		$this->socketServer->on('connection', function ($conn) {
//			/** @var ConnectionInterface $conn */
//			$this->writeln('rcv');
//		});

		$httpServer = new HttpServer($this->socketServer, $this->getEventLoop());
		$httpServer->on('request', array($this, 'handle'));
		$this->socketServer->listen($this->port, $this->ip);

		$this->writeln(Constants::MESSAGE_CLI_WELCOME . PHP_EOL);
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