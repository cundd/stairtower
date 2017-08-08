<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Server;

use Cundd\PersistentObjectStore\Configuration\ConfigurationManager;
use Cundd\PersistentObjectStore\Constants;
use Cundd\PersistentObjectStore\Domain\Model\Document;
use Cundd\PersistentObjectStore\Domain\Model\DocumentInterface;
use Cundd\PersistentObjectStore\Formatter\FormatterInterface;
use Cundd\PersistentObjectStore\Formatter\JsonFormatter;
use Cundd\PersistentObjectStore\Server\BodyParser\BodyParserInterface;
use Cundd\PersistentObjectStore\Server\Controller\ControllerInterface;
use Cundd\PersistentObjectStore\Server\Controller\ControllerResultInterface;
use Cundd\PersistentObjectStore\Server\Dispatcher\ControllerActionDispatcherInterface;
use Cundd\PersistentObjectStore\Server\Dispatcher\ServerActionDispatcherInterface;
use Cundd\PersistentObjectStore\Server\Dispatcher\SpecialHandlerActionDispatcherInterface;
use Cundd\PersistentObjectStore\Server\Dispatcher\StandardActionDispatcherInterface;
use Cundd\PersistentObjectStore\Server\Exception\InvalidBodyException;
use Cundd\PersistentObjectStore\Server\Exception\InvalidRequestControllerException;
use Cundd\PersistentObjectStore\Server\Exception\InvalidRequestMethodException;
use Cundd\PersistentObjectStore\Server\Exception\MissingLengthHeaderException;
use Cundd\PersistentObjectStore\Server\Handler\HandlerInterface;
use Cundd\PersistentObjectStore\Server\Handler\HandlerResultInterface;
use Cundd\PersistentObjectStore\Server\ValueObject\ControllerResult;
use Cundd\PersistentObjectStore\Server\ValueObject\DeferredResult;
use Cundd\PersistentObjectStore\Server\ValueObject\HandlerResult;
use Cundd\PersistentObjectStore\Server\ValueObject\NullResult;
use Cundd\PersistentObjectStore\Server\ValueObject\RawResultInterface;
use Cundd\PersistentObjectStore\Server\ValueObject\Request;
use Cundd\PersistentObjectStore\Server\ValueObject\RequestInfoFactory;
use Cundd\PersistentObjectStore\Server\ValueObject\RequestInterface;
use Cundd\PersistentObjectStore\Utility\ContentTypeUtility;
use Monolog\Logger;
use React\Http\Response;
use React\Http\Server as HttpServer;
use React\Socket\Server as SocketServer;
use React\Stream\BufferedSink;
use React\Stream\WritableStreamInterface;
use ReflectionClass;

/**
 * REST server
 */
class RestServer extends AbstractServer implements StandardActionDispatcherInterface, SpecialHandlerActionDispatcherInterface, ServerActionDispatcherInterface, ControllerActionDispatcherInterface
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
     * Request factory instance to transform React requests into Stairtower requests
     *
     * @var \Cundd\PersistentObjectStore\Server\ValueObject\RequestInfoFactory
     * @Inject
     */
    protected $requestFactory;

    /**
     * Handle the given request
     *
     * @param RequestInterface        $request
     * @param WritableStreamInterface $response
     */
    public function handle(RequestInterface $request, WritableStreamInterface $response): void
    {
        // If the configured log level is DEBUG log all requests
        static $debugLog = -1;
        if ($debugLog === -1) {
            $debugLog = ConfigurationManager::getSharedInstance()
                    ->getConfigurationForKeyPath('logLevel') <= Logger::DEBUG;
        }

        $requestResult = null;

        if ($debugLog) {
            $this->logger->debug(
                sprintf(
                    'Begin handle request %s %s %s',
                    $request->getMethod(),
                    $request->getPath(),
                    $request->getHttpVersion()
                )
            );
        }

        try {
            switch (true) {
                // MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
                // SERVER ACTION
                // MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
                case false !== RequestInfoFactory::getServerActionForRequest($request):
                    // Handle a very special server action
                    $this->handleServerAction(
                        RequestInfoFactory::getServerActionForRequest($request),
                        $request,
                        $response
                    );

                    return;

                // MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
                // CONTROLLER ACTION
                // MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
                case $request->getControllerClass():
                    $requestResult = $this->dispatchControllerAction($request, $response);
                    break;

                // MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
                // SPECIAL HANDLER ACTION
                // MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
                case $request->getAction():
                    $requestResult = $this->dispatchSpecialHandlerAction($request, $response);
                    break;

                // MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
                // STANDARD ACTION
                // MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM
                default:
                    $requestResult = $this->dispatchStandardAction($request, $response);
            }

            if ($requestResult !== null && $requestResult !== DeferredResult::instance()) {
                $this->handleResult($requestResult, $request, $response);
            }
        } catch (\Exception $exception) {
            $this->handleError($exception, $request, $response);
        }

        if ($debugLog) {
            $logMessageFormat = 'End handle request %s %s %s';
            if ($requestResult === DeferredResult::instance()) {
                $logMessageFormat = 'Wait to end handle deferred request %s %s %s';
            }
            $logMessage = sprintf(
                $logMessageFormat,
                $request->getMethod(),
                $request->getPath(),
                $request->getHttpVersion()
            );
            $this->logger->debug($logMessage);
        }
    }

    /**
     * Dispatches the standard action
     *
     * @param Request|RequestInterface         $request
     * @param WritableStreamInterface|Response $response
     * @return HandlerResultInterface Returns the Handler Result if the request is not delayed
     */
    public function dispatchStandardAction(RequestInterface $request, WritableStreamInterface $response)
    {
        $requestResult = null;
        $method = $request->getMethod();

        // The No Route action
        if (!$request->getDatabaseIdentifier()) {
            return $this->getHandlerForRequest($request)->noRoute($request);
        }

        switch ($method) {
            case 'POST':
            case 'PUT':
                $this->waitForBodyAndPerformHandlerAction($request, $response);
                $requestResult = DeferredResult::instance();
                break;

            case 'GET':
                $handler = $this->getHandlerForRequest($request);
                $requestResult = $handler->read($request);
                break;

            case 'DELETE':
                $handler = $this->getHandlerForRequest($request);
                $requestResult = $handler->delete($request);
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
     * Dispatches the given Controller/Action request action
     *
     * @param Request|RequestInterface         $request
     * @param WritableStreamInterface|Response $response
     * @return ControllerResultInterface|null Returns the Handler Result if the request is not delayed
     */
    public function dispatchControllerAction(
        RequestInterface $request,
        WritableStreamInterface $response
    ): ?ControllerResultInterface
    {
        $self = $this;
        $contentLength = 0;
        $controller = $this->getControllerForRequest($request);

        try {
            $contentLength = $this->getContentLengthFromRequest($request);
        } catch (MissingLengthHeaderException $exception) {
        }

        try {
            if ($contentLength) {
                $this->waitForBodyAndPerformCallback(
                    $request,
                    $response,
                    function ($_, $requestBody) use ($self, $controller, $request, $response) {
                        $request = RequestInfoFactory::copyWithBody($request, $requestBody);

                        return $self->invokeControllerActionWithRequest($request, $response, $controller);
                    },
                    true
                );
            } else {
                return $self->invokeControllerActionWithRequest($request, $response, $controller);
            }
        } catch (\Exception $exception) {
            $this->handleError($exception, $request, $response);
        }

        return null;
    }

    /**
     * Dispatches the given Controller/Action request action
     *
     * @param RequestInterface                 $request
     * @param WritableStreamInterface|Response $response
     * @return HandlerResultInterface Returns the Handler Result if the request is not delayed
     */
    public function dispatchSpecialHandlerAction(
        RequestInterface $request,
        WritableStreamInterface $response
    ): HandlerResultInterface {
        $handler = $this->getHandlerForRequest($request);

        return call_user_func([$handler, $request->getAction()], $request);
    }

    /**
     * Handles the given Controller/Action request action
     *
     * @param Request|RequestInterface         $request
     * @param WritableStreamInterface|Response $response
     * @param ControllerInterface              $controller
     * @return ControllerResultInterface Returns the Handler Result
     */
    public function invokeControllerActionWithRequest(
        RequestInterface $request,
        WritableStreamInterface $response,
        ControllerInterface $controller
    ): ControllerResultInterface {
        try {
            $result = $controller->processRequest($request, $response);
        } catch (\Exception $exception) {
            $this->writeln('Caught exception #%d: %s', $exception->getCode(), $exception->getMessage());
            $this->writeln($exception->getTraceAsString());

            $errorResponseBody = sprintf(
                'An error occurred while calling controller \'%s\' action \'%s\' %s',
                $request->getControllerClass(),
                $request->getAction(),
                $request->getBody() ? 'with a body' : 'without a body'
            );

            if ($this->getMode() === self::SERVER_MODE_DEVELOPMENT) {
                $errorResponseBody .= sprintf(
                    '%s#%d: %s%s%s',
                    PHP_EOL,
                    $exception->getCode(),
                    $exception->getMessage(),
                    PHP_EOL,
                    $exception->getTraceAsString()
                );
            }

            $contentType = $this->getContentTypeForRequest($request);
            if ($contentType === ContentType::XML_TEXT || $contentType === ContentType::HTML_TEXT) {
                $errorResponseBody = nl2br($errorResponseBody);
            }

            return new ControllerResult(500, $errorResponseBody, $contentType);
        }

        if ($result === null) {
            return new NullResult();
        }

        return $result;
    }

    /**
     * Dispatches the given server action
     *
     * @param string                           $serverAction
     * @param Request|RequestInterface         $request
     * @param WritableStreamInterface|Response $response
     */
    public function dispatchServerAction(
        string $serverAction,
        RequestInterface $request,
        WritableStreamInterface $response
    ) {
        $this->handleServerAction($serverAction, $request, $response);
    }

    /**
     * Handle the given request result
     *
     * @param HandlerResultInterface           $result
     * @param RequestInterface                 $request
     * @param Response|WritableStreamInterface $response
     */
    public function handleResult(
        HandlerResultInterface $result,
        RequestInterface $request,
        WritableStreamInterface $response
    ): void {
        if ($result instanceof RawResultInterface) {
            $response->writeHead(
                $result->getStatusCode(),
                ['Content-Type' => $result->getContentType()]
            );
            $responseData = $result->getData();
            if ($responseData !== null) {
                $response->end($responseData);
            } else {
                $response->end();
            }

        } elseif ($result instanceof ControllerResultInterface) {
            $response->writeHead(
                $result->getStatusCode(),
                $result->getHeaders()
            );
            $responseData = $result->getData();
            if ($responseData !== null) {
                $response->end($responseData);
            } else {
                $response->end();
            }

        } elseif ($result instanceof HandlerResultInterface) {
            $formatter = $this->getFormatterForRequest(
                $request instanceof Request ? $request : $this->requestFactory->buildRequestFromRawRequest($request)
            );
            $response->writeHead(
                $result->getStatusCode(),
                [
                    'Content-Type' => ContentTypeUtility::convertSuffixToContentType($formatter->getContentSuffix())
                        . '; charset=utf-8',
                ]
            );
            $responseData = $result->getData();
            if ($responseData !== null) {
                $response->end($formatter->format($responseData));
            } else {
                $response->end();
            }

        } elseif ($result === null) {
            $formatter = $this->getFormatterForRequest($request);
            $response->writeHead(
                204,
                [
                    'Content-Type' => ContentTypeUtility::convertSuffixToContentType($formatter->getContentSuffix())
                        . '; charset=utf-8',
                ]
            );
            $response->end($formatter->format('No content'));
        } else {
            throw new \UnexpectedValueException('Handler result is of type ' . gettype($result), 1413210970);
        }
    }

    /**
     * Waits for the total request body and performs the needed action
     *
     * @param Request  $request
     * @param Response $response
     */
    public function waitForBodyAndPerformHandlerAction($request, $response)
    {
        $self = $this;
        $this->waitForBodyAndPerformCallback(
            $request,
            $response,
            function ($originalRequest, $requestBodyParsed) use ($request, $self) {
                $handler = $self->getHandlerForRequest($request);

                /** @var \React\Http\Request $originalRequest */
                if ($originalRequest->getMethod() === 'PUT' && $request->getDataIdentifier()) {
                    $requestResult = $handler->update(
                        $request,
                        $requestBodyParsed
                    );
                } else {
                    $requestResult = $handler->create(
                        $request,
                        $requestBodyParsed
                    );
                }

                return $requestResult;
            },
            false
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
    public function waitForBodyAndPerformCallback($request, $response, callable $callback, bool $allowRawBody)
    {
        $requestBody = '';
        $contentLength = $this->getContentLengthFromRequest($request);
        $receivedData = 0;

        $logger = $this->logger;
        $logger->debug(
            sprintf(
                '< Start deferred request with content-length %d %s %s %s',
                $contentLength,
                $request->getMethod(),
                $request->getPath(),
                $request->getHttpVersion()
            )
        );

        if ($contentLength <= 0) {
            $requestResult = $callback($request, null);
            if ($requestResult !== null) {
                $this->handleResult($requestResult, $request, $response);
            }

            return;
        }


        $request->on(
            'data',
            function ($data) use (
                $request,
                $response,
                &$requestBody,
                &$receivedData,
                $contentLength,
                $callback,
                $allowRawBody,
                $logger
            ) {
                $logger->debug('Receive data');
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
                                $requestBodyParsed = $this->getBodyParserForRequest($request)->parse(
                                    $requestBody,
                                    $request
                                );
                            } catch (InvalidBodyException $parserException) {
                                $logger->alert(
                                    '! No matching body parser'
                                );

                                if (!$allowRawBody) {
                                    throw $parserException;
                                }
                            }
                        }

                        $logger->debug(
                            sprintf(
                                '> Handle deferred request %s %s %s',
                                $request->getMethod(),
                                $request->getPath(),
                                $request->getHttpVersion()
                            )
                        );

                        $requestResult = $callback($request, $requestBodyParsed);
                        if ($requestResult !== null) {
                            $this->handleResult($requestResult, $request, $response);
                        }
                    }
                } catch (\Exception $exception) {
                    $this->handleError($exception, $request, $response);
                }
            }
        );

        $request->on(
            'end',
            function () use ($logger) {
                $this->logger->debug('X End of connection', func_get_args());
            }
        );
    }

    /**
     * Returns the content length for the given request
     *
     * @param Request $request
     * @return int
     */
    protected function getContentLengthFromRequest($request)
    {
        $headers = $request->getHeaders();
        $headerNamesToCheck = ['Content-Length', 'Content-length', 'content-length'];
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
     * @param Request|RequestInterface $request
     * @return HandlerInterface
     */
    public function getHandlerForRequest(RequestInterface $request)
    {
        return $this->diContainer->get(RequestInfoFactory::getHandlerClassForRequest($request));
    }

    /**
     * Returns the body parser for the given request
     *
     * @param Request|RequestInterface $request
     * @return BodyParserInterface
     */
    public function getBodyParserForRequest(RequestInterface $request): BodyParserInterface
    {
        $header = $request->getHeaders();
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
     * @param Request|RequestInterface $request
     * @return string
     */
    public function getContentTypeForRequest(RequestInterface $request): string
    {
        return $request->getContentType();
    }

    /**
     * Returns the formatter for the given request
     *
     * @param Request|RequestInterface $request
     * @return FormatterInterface
     */
    public function getFormatterForRequest(RequestInterface $request): FormatterInterface
    {
        if ($this->getContentTypeForRequest($request) === ContentType::XML_TEXT) {
            // TODO: Implement the XML formatter
            $formatter = 'Cundd\\PersistentObjectStore\\Formatter\\XmlFormatter';
        } else {
            $formatter = JsonFormatter::class;
        }

        return $this->diContainer->get($formatter);
    }

    public function getControllerForRequest(RequestInterface $request): ?ControllerInterface
    {
        if (!$request->getControllerClass()) {
            return null;
        }
        $controller = $this->diContainer->get($request->getControllerClass());
        if (!$controller) {
            throw new InvalidRequestControllerException(
                sprintf(
                    'Could not get valid controller implementation for class "%s"',
                    $request->getControllerClass()
                ),
                1420584056
            );
        }

        if (!$controller instanceof ControllerInterface) {
            throw new InvalidRequestControllerException(
                'Detected controller is not an instance of Cundd\\PersistentObjectStore\\Server\\Controller\\ControllerInterface',
                1420712698
            );
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
        return BufferedSink::createPromise($request->getOriginalRequest());
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

        $httpServer = new HttpServer($this->socketServer);
        $httpServer->on('request', [$this, 'prepareAndHandle']);
        $this->socketServer->listen($this->port, $this->ip);

        $this->writeln(Constants::MESSAGE_CLI_WELCOME . PHP_EOL);
        $this->writeln('Start listening on %s:%s', $this->ip, $this->port);
        $this->logger->info(sprintf('Start listening on %s:%s', $this->ip, $this->port));
    }

    /**
     * Handle the given request
     *
     * @param \React\Http\Request|RequestInterface $request
     * @param WritableStreamInterface              $response
     */
    public function prepareAndHandle($request, WritableStreamInterface $response)
    {
        // Immediately close ignored requests
        if ($this->getIgnoreRequest($request)) {
            $response->end();
        } else {
            try {
                $requestInfo = $this->requestFactory->buildRequestFromRawRequest($request, true);
                $this->handle($requestInfo, $response);
            } catch (\Exception $error) {
                $this->handleError($error, $request, $response);
            }
        }
    }

    /**
     * Returns if the given request should be ignored
     *
     * @param Request|\React\Http\Request $request
     * @return bool
     */
    protected function getIgnoreRequest($request)
    {
        if ($request instanceof RequestInterface || $request instanceof \React\Http\Request) {
            if ($request->getMethod() === 'GET' && $request->getPath() === '/favicon.ico') {
                return true;
            }

            return false;
        }

        return true;
    }

    /**
     * Returns if the given controller action requires a Document as parameter
     *
     * TODO: Move this functionality into a separate class
     *
     * @param string|object $controller   Class name or instance
     * @param string        $actionMethod Method name
     * @return int Returns 1 if a Document is required, 2 if it is optional otherwise 0
     */
    protected function getControllerActionRequiresDocumentArgument($controller, $actionMethod)
    {
        static $controllerActionRequiresDocumentCache = [];
        $controllerClass = is_string($controller) ? $controller : get_class($controller);
        $controllerActionIdentifier = $controllerClass . '::' . $actionMethod;

        if (isset($controllerActionRequiresDocumentCache[$controllerActionIdentifier])) {
            return $controllerActionRequiresDocumentCache[$controllerActionIdentifier];
        }

        $classReflection = new ReflectionClass($controllerClass);
        $methodReflection = $classReflection->getMethod($actionMethod);
        $controllerActionRequiresDocumentCache[$controllerActionIdentifier] = 0;
        foreach ($methodReflection->getParameters() as $parameter) {
            if ($parameter->getClass()) {
                $argumentClassName = trim($parameter->getClass()->getName());
                if ($argumentClassName === Document::class || $argumentClassName === DocumentInterface::class) {
                    $controllerActionRequiresDocumentCache[$controllerActionIdentifier] = ($parameter->isOptional()
                        ? 2
                        : 1);
                    break;
                }
            }
        }

        return $controllerActionRequiresDocumentCache[$controllerActionIdentifier];
    }
}
