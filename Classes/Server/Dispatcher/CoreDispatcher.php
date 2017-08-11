<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server\Dispatcher;

use Cundd\Stairtower\Configuration\ConfigurationManager;
use Cundd\Stairtower\LogicException;
use Cundd\Stairtower\Server\BodyParser\BodyParserInterface;
use Cundd\Stairtower\Server\BodyParser\FormDataBodyParser;
use Cundd\Stairtower\Server\BodyParser\JsonBodyParser;
use Cundd\Stairtower\Server\ContentType;
use Cundd\Stairtower\Server\Controller\ControllerInterface;
use Cundd\Stairtower\Server\Controller\ControllerResultInterface;
use Cundd\Stairtower\Server\Exception\InvalidBodyException;
use Cundd\Stairtower\Server\Exception\InvalidRequestControllerException;
use Cundd\Stairtower\Server\Exception\InvalidRequestMethodException;
use Cundd\Stairtower\Server\Handler\HandlerInterface;
use Cundd\Stairtower\Server\Handler\HandlerResultInterface;
use Cundd\Stairtower\Server\OutputWriterTrait;
use Cundd\Stairtower\Server\ResponseBuilder;
use Cundd\Stairtower\Server\ServerInterface;
use Cundd\Stairtower\Server\ValueObject\ControllerResult;
use Cundd\Stairtower\Server\ValueObject\ExceptionResult;
use Cundd\Stairtower\Server\ValueObject\HandlerResult;
use Cundd\Stairtower\Server\ValueObject\Request;
use Cundd\Stairtower\Server\ValueObject\RequestInfoFactory;
use Cundd\Stairtower\Server\ValueObject\RequestInterface;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use React\Http\Response;
use React\Promise\Promise;
use React\Stream\ReadableStreamInterface;

/**
 * REST server
 */
class CoreDispatcher implements StandardActionDispatcherInterface, SpecialHandlerActionDispatcherInterface, ServerActionDispatcherInterface, ControllerActionDispatcherInterface
{
    use OutputWriterTrait;

    /**
     * DI container
     *
     * @var \DI\Container
     * @Inject
     */
    private $diContainer;

    /**
     * Request factory instance to transform React requests into Stairtower requests
     *
     * @var \Cundd\Stairtower\Server\ValueObject\RequestInfoFactory
     * @Inject
     */
    private $requestFactory;

    /**
     * @var ResponseBuilder
     */
    private $responseBuilder;

    /**
     * @var \Psr\Log\LoggerInterface
     * @inject
     */
    private $logger;

    /**
     * @var ServerInterface
     */
    private $server;

    /**
     * CoreDispatcher constructor
     *
     * @param RequestInfoFactory $requestFactory
     * @param ResponseBuilder    $responseBuilder
     * @param ContainerInterface $container
     * @param LoggerInterface    $logger
     * @param ServerInterface    $server
     */
    public function __construct(
        RequestInfoFactory $requestFactory,
        ResponseBuilder $responseBuilder,
        ContainerInterface $container,
        LoggerInterface $logger,
        ServerInterface $server
    ) {
        $this->requestFactory = $requestFactory;
        $this->responseBuilder = $responseBuilder;
        $this->diContainer = $container;
        $this->logger = $logger;
        $this->server = $server;
    }

    /**
     * Dispatch the Request
     *
     * @param ServerRequestInterface $serverRequest
     * @return Promise
     */
    public function dispatch(ServerRequestInterface $serverRequest)
    {
        return new Promise(
            function (callable $resolve, callable $reject) use ($serverRequest) {
                $this->logger->info(__LINE__);
                // Immediately close ignored requests
                if ($this->getIgnoreRequest($serverRequest)) {
                    $resolve(new Response(404, ['Content-Type' => 'text/plain']));

                    return;
                }

                $promiseCallback = new PromiseCallback($resolve, $reject);
                $request = $this->requestFactory->buildRequestFromRawRequest($serverRequest);

                try {
                    $this->waitForBodyDispatchAndLog($request, $promiseCallback, true);
                } catch (\Throwable $error) {
                    echo $error;
                    $this->logger->error((string)$error);
                    $resolve($this->responseBuilder->buildErrorResponse($error, $request));
                }
            }
        );
    }

    /**
     * Waits for the total request body and performs the needed action
     *
     * @param RequestInterface $request      Incoming request
     * @param PromiseCallback  $promiseCallback
     * @param bool             $allowRawBody If set to true Body Parser exceptions will be ignored
     */
    private function waitForBodyAndDispatch(
        RequestInterface $request,
        PromiseCallback $promiseCallback,
        bool $allowRawBody
    ): void {
        $contentLength = $this->getContentLengthFromRequest($request);
        $rawBody = '';
        $receivedDataLength = 0;

        $this->logger->debug(
            sprintf(
                '< Start deferred request with content-length %d %s %s %s',
                $contentLength,
                $request->getMethod(),
                $request->getPath(),
                $request->getHttpVersion()
            )
        );

        if ($contentLength <= 0) {
            $requestResult = $this->dispatchInternal($request);

            $this->logger->debug('hello ' . __LINE__);
            $promiseCallback->resolve($this->responseBuilder->buildResponseForResult($requestResult, $request));

            return;
        }


        $bodyStream = $request->getBody();
        if (!$bodyStream instanceof ReadableStreamInterface) {
            throw new LogicException(
                sprintf(
                    'Request body is not an instance of %s but',
                    ReadableStreamInterface::class,
                    is_object($bodyStream) ? get_class($bodyStream) : gettype($bodyStream)
                ),
                1502448377
            );
        }
        $bodyStream->on(
            'data',
            function ($data) use (
                $request,
                &$rawBody,
                &$receivedDataLength,
                $contentLength,
                $allowRawBody
            ) {
                $this->logger->debug('Receive data');
                var_dump($data);
                $rawBody .= $data;
                $receivedDataLength += strlen($data);
            }
        );

        $bodyStream->on(
            'end',
            function () use ($request, $promiseCallback, $allowRawBody, &$rawBody) {
                $body = $this->parseBody($request, $allowRawBody, $rawBody);

                $this->logger->debug(
                    sprintf(
                        'End stream %s %s %s',
                        $request->getMethod(),
                        $request->getPath(),
                        $request->getHttpVersion()
                    )
                );

                $requestResult = $this->dispatchInternal($request->withBody($body));
                $promiseCallback->resolve($this->responseBuilder->buildResponseForResult($requestResult, $request));
            }
        );

        // An error occurs e.g. on invalid chunked encoded data or an unexpected 'end' event
        $bodyStream->on(
            'error',
            function (\Exception $exception) use ($request, $promiseCallback, &$receivedDataLength, $contentLength) {
                $this->logger->debug('errorrrrrr .' . __LINE__);
                $promiseCallback->resolve($this->responseBuilder->buildErrorResponse($exception, $request));
                // $response = new Response(
                //     400,
                //     ['Content-Type' => 'text/plain'],
                //     sprintf('An error occurred while reading at length: %s of %s', $receivedDataLength, $contentLength)
                // );
                // $promiseCallback->resolve($response);
            }
        );
    }

    private function waitForBodyDispatchAndLog(
        RequestInterface $request,
        PromiseCallback $promiseCallback,
        bool $allowRawBody
    ): void {
        // If the configured log level is DEBUG log all requests
        static $debugLog = -1;
        if ($debugLog === -1) {
            $debugLog = ConfigurationManager::getSharedInstance()
                    ->getConfigurationForKeyPath('logLevel') <= Logger::DEBUG;
        }

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

        $this->waitForBodyAndDispatch($request, $promiseCallback, $allowRawBody);

        if ($debugLog) {
            $this->logger->debug(
                sprintf(
                    'End handle request %s %s %s',
                    $request->getMethod(),
                    $request->getPath(),
                    $request->getHttpVersion()
                )
            );
        }
    }

    private function dispatchInternal(RequestInterface $request): HandlerResultInterface
    {
        try {
            switch (true) {
                // Server action
                case '' !== RequestInfoFactory::getServerActionForRequest($request):
                    // Handle a very special server action
                    $action = RequestInfoFactory::getServerActionForRequest($request);

                    return $this->dispatchServerAction($action, $request);

                // Controller action
                case '' !== $request->getControllerClass():
                    return $this->dispatchControllerAction($request);

                // Special handler action
                case '' !== $request->getAction():
                    return $this->dispatchSpecialHandlerAction($request);

                // Standard action
                default:
                    return $this->dispatchStandardAction($request);
            }
        } catch (\Throwable $error) {
            $this->logger->error($error->getMessage(), ['trace' => $error->getTraceAsString()]);

            return new ExceptionResult($error);
        }
    }

    private function parseBody(
        RequestInterface $request,
        bool $allowRawBody,
        string $rawBody
    ) {
        if (!$rawBody) {
            return null;
        }

        try {
            return $this->getBodyParserForRequest($request)->parse($rawBody, $request);
        } catch (InvalidBodyException $parserException) {
            $this->logger->alert('! No matching body parser');

            if ($allowRawBody) {
                return $rawBody;
            }

            throw $parserException;
        }
    }

    public function dispatchStandardAction(RequestInterface $request): HandlerResultInterface
    {
        $method = $request->getMethod();
        $handler = $this->getHandlerForRequest($request);

        // The "No Route" action
        if (!$request->getDatabaseIdentifier()) {
            return $handler->noRoute($request);
        }

        switch ($method) {
            /** @noinspection PhpMissingBreakStatementInspection */
            case 'PUT':
                if ($request->getDataIdentifier()) {
                    return $handler->update($request, $request->getBody());
                }
            // else treat like a POST

            case 'POST':
                return $handler->create($request, $request->getBody());

            case 'GET':
                return $handler->read($request);

            case 'DELETE':
                return $handler->delete($request);

            default:
                return new HandlerResult(
                    405,
                    new InvalidRequestMethodException(sprintf('Request method "%s" not valid', $method), 1413033763)
                );
        }
    }

    public function dispatchControllerAction(RequestInterface $request): ControllerResultInterface
    {
        $controller = $this->getControllerForRequest($request);

        return $this->invokeControllerActionWithRequest($request, $controller);
    }

    public function dispatchSpecialHandlerAction(
        RequestInterface $request
    ): HandlerResultInterface {
        $handler = $this->getHandlerForRequest($request);
        $action = $request->getAction();

        return call_user_func(
            [
                $handler,
                $action,
            ],
            $request
        );
    }

    public function invokeControllerActionWithRequest(
        RequestInterface $request,
        ControllerInterface $controller
    ): ControllerResultInterface {
        try {
            return $controller->processRequest($request);
        } catch (\Exception $exception) {
            $this->writeln(
                'Caught exception #%d: %s',
                $exception->getCode(),
                $exception->getMessage(),
                ['trace' => $exception->getTrace()]
            );

            $errorResponseBody = sprintf(
                'An error occurred while calling controller \'%s\' action \'%s\' %s',
                $request->getControllerClass(),
                $request->getAction(),
                $request->getBody() ? 'with a body' : 'without a body'
            );

            if ($this->server->getMode() === ServerInterface::SERVER_MODE_DEVELOPMENT) {
                $errorResponseBody .= sprintf(
                    '%s#%d: %s%s%s',
                    PHP_EOL,
                    $exception->getCode(),
                    $exception->getMessage(),
                    PHP_EOL,
                    $exception->getTraceAsString()
                );
            }

            $contentType = $request->getContentType();
            if ($contentType === ContentType::XML_TEXT || $contentType === ContentType::HTML_TEXT) {
                $errorResponseBody = nl2br($errorResponseBody);
            }

            return new ControllerResult(500, $errorResponseBody, $contentType);
        }
    }

    public function dispatchServerAction(string $serverAction, RequestInterface $request): HandlerResultInterface
    {
        if ($this->server instanceof ServerActionDispatcherInterface) {
            return $this->server->dispatchServerAction($serverAction, $request);
        }

        throw new LogicException('Server is not an instance of ' . ServerActionDispatcherInterface::class);
    }

    /**
     * Returns the content length for the given request
     *
     * @param RequestInterface $request
     * @return int
     */
    private function getContentLengthFromRequest(RequestInterface $request): int
    {
        if ($request->getBody() instanceof ReadableStreamInterface) {
            return $request->getBody()->getSize();
        }

        $header = $request->getHeader('Content-Length');

        return (int)reset($header);
    }

    /**
     * Returns the handler for the given request
     *
     * @param RequestInterface $request
     * @return HandlerInterface
     */
    public function getHandlerForRequest(RequestInterface $request): HandlerInterface
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
        $bodyParser = JsonBodyParser::class;

        $header = $request->getHeader('Content-Type');
        if (!empty($header)) {
            $contentType = reset($header);
            if (substr($contentType, 0, 19) === 'multipart/form-data') {
                throw new InvalidBodyException(sprintf('No body parser for Content-Type "%s" found', $contentType));
            } elseif ($contentType === 'application/x-www-form-urlencoded') {
                $bodyParser = FormDataBodyParser::class;
            }
        }

        return $this->diContainer->get($bodyParser);
    }

    public function getControllerForRequest(
        RequestInterface $request
    ): ?ControllerInterface
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
                sprintf('Detected controller is not an instance of %s', ControllerInterface::class),
                1420712698
            );
        }

        return $controller;
    }

    /**
     * Returns if the given request should be ignored
     *
     * @param RequestInterface|ServerRequestInterface $request
     * @return bool
     */
    private function getIgnoreRequest($request): bool
    {
        if ($request instanceof RequestInterface || $request instanceof ServerRequestInterface) {
            return $request->getMethod() === 'GET' && $request->getUri()->getPath() === '/favicon.ico';
        }

        return true;
    }
}
