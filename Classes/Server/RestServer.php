<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 15.08.14
 * Time: 19:38
 */

namespace Cundd\PersistentObjectStore\Server;

use Cundd\PersistentObjectStore\Configuration\ConfigurationManager;
use Cundd\PersistentObjectStore\Constants;
use Cundd\PersistentObjectStore\Formatter\FormatterInterface;
use Cundd\PersistentObjectStore\Server\BodyParser\BodyParserInterface;
use Cundd\PersistentObjectStore\Server\Controller\ControllerResultInterface;
use Cundd\PersistentObjectStore\Server\Exception\InvalidBodyException;
use Cundd\PersistentObjectStore\Server\Exception\InvalidRequestControllerException;
use Cundd\PersistentObjectStore\Server\Exception\InvalidRequestMethodException;
use Cundd\PersistentObjectStore\Server\Exception\MissingLengthHeaderException;
use Cundd\PersistentObjectStore\Server\Exception\RequestMethodNotImplementedException;
use Cundd\PersistentObjectStore\Server\Handler\HandlerInterface;
use Cundd\PersistentObjectStore\Server\Handler\HandlerResultInterface;
use Cundd\PersistentObjectStore\Server\ValueObject\ControllerResult;
use Cundd\PersistentObjectStore\Server\ValueObject\HandlerResult;
use Cundd\PersistentObjectStore\Server\ValueObject\RequestInfo;
use Cundd\PersistentObjectStore\Server\ValueObject\RequestInfoFactory;
use Cundd\PersistentObjectStore\Utility\ContentTypeUtility;
use Monolog\Logger;
use React\Http\Request;
use React\Http\Response;
use React\Http\Server as HttpServer;
use React\Socket\Server as SocketServer;
use React\Stream\BufferedSink;

/**
 * REST server
 *
 * @package Cundd\PersistentObjectStore
 */
class RestServer extends AbstractServer
{
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
    public function handle($request, $response)
    {
        // If the configured log level is DEBUG log all requests
        static $logRequests = -1;
        if ($logRequests === -1) {
            $logRequests = ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('logLevel') <= Logger::DEBUG;
        }
        if ($logRequests) {
            $this->logger->debug(
                sprintf('Request %s %s %s', $request->getMethod(), $request->getPath(), $request->getHttpVersion())
            );
        }

        try {
            // MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
            // SERVER ACTION
            // MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
            $serverAction = RequestInfoFactory::getServerActionForRequest($request);
            if ($serverAction) { // Handle a very special server action
                $this->handleServerAction($serverAction, $request, $response);
                return;
            }
            $handler     = $this->getHandlerForRequest($request);
            $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest($request);


            // MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
            // CONTROLLER ACTION
            // MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
            if ($requestInfo->getControllerClass()) {
                $requestResult = $this->handleControllerAction($request, $response);
            }

            // MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
            // SPECIAL HANDLER ACTION
            // MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
            elseif ($requestInfo->getAction()) {
                $requestResult = call_user_func(array($handler, $requestInfo->getAction()), $requestInfo);
            }

            // MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
            // NO ROUTE ACTION
            // MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
            elseif (!$requestInfo->getDatabaseIdentifier()) {
                $requestResult = $handler->noRoute($requestInfo);
            }

            // MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
            // STANDARD ACTION
            // MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
            else {
                $requestResult = $this->handleStandardAction($request, $response);
            }

            if ($requestResult) {
                $this->handleResult($requestResult, $request, $response);
            }
        } catch (\Exception $exception) {
            $this->handleError($exception, $request, $response);
        }
    }

    /**
     * Handles the standard action
     *
     * @param \React\Http\Request  $request
     * @param \React\Http\Response $response
     * @return HandlerResultInterface Returns the Handler Result if the request is not delayed
     */
    public function handleStandardAction($request, $response)
    {
        $requestResult = null;
        $method        = $request->getMethod();
        $requestInfo   = RequestInfoFactory::buildRequestInfoFromRequest($request);

        switch ($method) {
            case 'POST':
            case 'PUT':
            $this->waitForBodyAndPerformHandlerAction($request, $response, $requestInfo);
                break;

            case 'GET':
                $handler       = $this->getHandlerForRequest($request);
                $requestResult = $handler->read($requestInfo);
                break;

            case 'DELETE':
                $handler       = $this->getHandlerForRequest($request);
                $requestResult = $handler->delete($requestInfo);
                break;

            default:
                $requestResult = new HandlerResult(
                    405,
                    new InvalidRequestMethodException(sprintf('Request method "%s" not valid', $method), 1413033763)
                );
        }
        return $requestResult;
    }

    /**
     * Handles the given Controller/Action request action
     *
     * @param \React\Http\Request  $request
     * @param \React\Http\Response $response
     * @return HandlerResultInterface Returns the Handler Result if the request is not delayed
     */
    public function handleControllerAction($request, $response)
    {
        $self          = $this;
        $contentLength = 0;
        $requestInfo   = RequestInfoFactory::buildRequestInfoFromRequest($request);
        $controller    = $this->getControllerForRequest($request);

        try {
            $contentLength = $this->getContentLengthFromRequest($request);
        } catch (MissingLengthHeaderException $exception) {
        }

        try {
            if ($contentLength) {
                $this->waitForBodyAndPerformCallback(
                    $request, $response,
                    function ($request, $requestBody) use ($self, $controller, $requestInfo, $response) {
                        return $self->invokeControllerActionWithRequestInfo(
                            $requestInfo, $response, $controller, $requestBody
                        );
                    },
                    true
                );
            } else {
                return $self->invokeControllerActionWithRequestInfo($requestInfo, $response, $controller);
            }
        } catch (\Exception $exception) {
            $this->handleError($exception, $request, $response);
        }
        return null;
    }

    /**
     * Handles the given Controller/Action request action
     *
     * @param RequestInfo $requestInfo
     * @param Response    $response
     * @param object      $controller
     * @param null        $requestBody
     * @return HandlerResultInterface Returns the Handler Result
     */
    public function invokeControllerActionWithRequestInfo($requestInfo, $response, $controller, $requestBody = null)
    {
        if (!method_exists($controller, $requestInfo->getAction())) {
            throw new RequestMethodNotImplementedException(
                sprintf('Request method %s is not defined', $requestInfo->getAction()),
                1420551044
            );
        }

        $result      = null;
        $requestInfo = RequestInfoFactory::copyWithBody($requestInfo, $requestBody);

        try {
            $result = call_user_func_array(
                array($controller, $requestInfo->getAction()),
                array($requestInfo)
            );
        } catch (\Exception $exception) {
            $this->writeln('Caught exception #%d: %s', $exception->getCode(), $exception->getMessage());
            $this->writeln($exception->getTraceAsString());
            return new ControllerResult(
                500,
                sprintf(
                    'An error occurred while calling controller \'%s\' action \'%s\' %s',
                    $requestInfo->getControllerClass(),
                    $requestInfo->getAction(),
                    $requestInfo->getBody() ? 'with a body' : 'without a body'
                ),
                $this->getContentTypeForRequest($requestInfo->getRequest())
            );
        }

        if ($result instanceof HandlerResultInterface) {
            return $result;
        }
        return new ControllerResult(200, $result);
    }

    /**
     * Handle the given request result
     *
     * @param HandlerResultInterface $result
     * @param Request                $request
     * @param Response               $response
     */
    public function handleResult($result, $request, $response)
    {
        if ($result instanceof ControllerResultInterface) {
            $response->writeHead(
                $result->getStatusCode(),
                array('Content-Type' => $result->getContentType() . '; charset=utf-8')
            );
            $responseData = $result->getData();
            if ($responseData) {
                $response->end($responseData);
            } else {
                $response->end();
            }

        } elseif ($result instanceof HandlerResultInterface) {
            $formatter = $this->getFormatterForRequest($request);
            $response->writeHead(
                $result->getStatusCode(),
                array('Content-Type' => ContentTypeUtility::convertSuffixToContentType($formatter->getContentSuffix()) . '; charset=utf-8')
            );
            $responseData = $result->getData();
            if ($responseData) {
                $response->end($formatter->format($responseData));
            } else {
                $response->end();
            }

        } elseif ($result === null) {
            $formatter = $this->getFormatterForRequest($request);
            $response->writeHead(
                204,
                array('Content-Type' => ContentTypeUtility::convertSuffixToContentType($formatter->getContentSuffix()) . '; charset=utf-8')
            );
            $response->end($formatter->format('No content'));
        } else {
            throw new \UnexpectedValueException('Handler result is of type ' . gettype($result), 1413210970);
        }
    }

    /**
     * Waits for the total request body and performs the needed action
     *
     * @param Request     $request
     * @param Response    $response
     * @param RequestInfo $requestInfo
     */
    public function waitForBodyAndPerformHandlerAction($request, $response, $requestInfo)
    {
        $self = $this;
        $this->waitForBodyAndPerformCallback(
            $request, $response, function ($request, $requestBodyParsed) use ($requestInfo, $self) {
            /** @var Request $request */
            if ($request->getMethod() === 'PUT' && $requestInfo->getDataIdentifier()) {
                $requestResult = $self->getHandlerForRequest($request)->update($requestInfo,
                    $requestBodyParsed);
            } else {
                $requestResult = $self->getHandlerForRequest($request)->create($requestInfo,
                    $requestBodyParsed);
            }
            return $requestResult;
        }, false
        );
    }

    /**
     * Waits for the total request body and performs the needed action
     *
     * @param Request  $request      Incoming request
     * @param Response $response     Outgoing response to write the result to
     * @param Callback $callback     Callback to invoke
     * @param bool     $allowRawBody If set to true Body Parser exceptions will be ignored
     */
    public function waitForBodyAndPerformCallback($request, $response, $callback, $allowRawBody)
    {
        $self = $this;

        $requestBody   = '';
        $contentLength = $this->getContentLengthFromRequest($request);
        $receivedData  = 0;
        $request->on('data',
            function ($data) use (
                $self,
                $request,
                $response,
                &$requestBody,
                &$receivedData,
                $contentLength,
                $callback,
                $allowRawBody
            ) {
                try {
                    $requestBody .= $data;
                    $receivedData += strlen($data);
                    if ($receivedData >= $contentLength) {

                        $requestBodyParsed = null;
                        if ($allowRawBody) {
                            $requestBodyParsed = $requestBody;
                        }
                        if ($requestBody) {
                            try {
                                $requestBodyParsed = $self->getBodyParserForRequest($request)->parse(
                                    $requestBody,
                                    $request
                                );
                            } catch (InvalidBodyException $parserException) {
                                if (!$allowRawBody) {
                                    throw $parserException;
                                }
                            }
                        }

                        $requestResult = $callback($request, $requestBodyParsed);
                        if ($requestResult !== null) {
                            $self->handleResult($requestResult, $request, $response);
                        }
                    }
                } catch (\Exception $exception) {
                    $this->handleError($exception, $request, $response);
                }
            });
    }

    /**
     * Returns the content length for the given request
     *
     * @param Request $request
     * @return int
     */
    protected function getContentLengthFromRequest($request)
    {
        $headers            = $request->getHeaders();
        $headerNamesToCheck = array('Content-Length', 'Content-length', 'content-length');
        foreach ($headerNamesToCheck as $headerName) {
            if (isset($headers[$headerName])) {
                return $headers[$headerName];
            }
        }
        throw new MissingLengthHeaderException('Could not detect the Content-Length', 1413473195);
    }

    /**
     * Returns the handler for the given request
     *
     * @param Request $request
     * @return HandlerInterface
     */
    public function getHandlerForRequest(Request $request)
    {
        return $this->diContainer->get(RequestInfoFactory::getHandlerClassForRequest($request));
    }

    /**
     * Returns the body parser for the given request
     *
     * @param Request $request
     * @return BodyParserInterface
     */
    public function getBodyParserForRequest(Request $request)
    {
        $header     = $request->getHeaders();
        $bodyParser = 'Cundd\\PersistentObjectStore\\Server\\BodyParser\\JsonBodyParser';
        if (isset($header['Content-Type'])) {
            $contentType = $header['Content-Type'];
            if (substr($contentType, 0, 19) === 'multipart/form-data') {
                throw new InvalidBodyException(sprintf('No body parser for Content-Type "%s" found', $contentType));
            } elseif ($contentType === 'application/x-www-form-urlencoded') {
                $bodyParser = 'Cundd\\PersistentObjectStore\\Server\\BodyParser\\FormDataBodyParser';
            }
        }
        return $this->diContainer->get($bodyParser);
    }

    /**
     * Returns the requested content type
     *
     * @param Request $request
     * @return string
     */
    public function getContentTypeForRequest(Request $request)
    {
        try {
            return RequestInfoFactory::buildRequestInfoFromRequest($request)->getContentType();
        } catch (\Exception $exception) {
        }
        return ContentType::JSON_APPLICATION;
    }

    /**
     * Returns the formatter for the given request
     *
     * @param Request $request
     * @return FormatterInterface
     */
    public function getFormatterForRequest(Request $request)
    {
        if ($this->getContentTypeForRequest($request) === ContentType::XML_TEXT) {
            $formatter = 'Cundd\\PersistentObjectStore\\Formatter\\XmlFormatter';
        } else {
            $formatter = 'Cundd\\PersistentObjectStore\\Formatter\\JsonFormatter';
        }
        return $this->diContainer->get($formatter);
    }

    /**
     * Returns the Controller instance for hte given request or false if none will be used
     *
     * @param Request $request
     * @return object
     */
    public function getControllerForRequest($request)
    {
        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest($request);
        if (!$requestInfo->getControllerClass()) {
            return false;
        }
        $controller = $this->diContainer->get($requestInfo->getControllerClass());
        if (!$controller) {
            throw new InvalidRequestControllerException(
                sprintf(
                    'Could not get valid controller implementation for class "%s"',
                    $requestInfo->getControllerClass()
                ),
                1420584056
            );
        }

        if (method_exists($controller, 'initialize')) {
            $controller->initialize();
        }
        if (method_exists($controller, 'setRequest')) {
            $controller->setRequest($request);
        }
        if (method_exists($controller, 'setRequestInfo')) {
            $controller->setRequestInfo($requestInfo);
        }
        return $controller;
    }

    /**
     * Returns a promise for the request body of the given request
     *
     * @param Request $request
     * @return \React\Promise\Promise
     */
    public function getRequestBodyPromiseForRequest($request)
    {
        return BufferedSink::createPromise($request);
    }

    public function stop()
    {
        $this->socketServer->shutdown();
        parent::stop();
    }

    /**
     * Create and configure the server objects
     */
    protected function setupServer()
    {
        $this->socketServer = new SocketServer($this->getEventLoop());

        $httpServer = new HttpServer($this->socketServer, $this->getEventLoop());
        $httpServer->on('request', array($this, 'handle'));
        $this->socketServer->listen($this->port, $this->ip);

        $this->writeln(Constants::MESSAGE_CLI_WELCOME . PHP_EOL);
        $this->writeln('Start listening on %s:%s', $this->ip, $this->port);
        $this->logger->info(sprintf('Start listening on %s:%s', $this->ip, $this->port));
    }

}