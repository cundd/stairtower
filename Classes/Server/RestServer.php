<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server;

use Cundd\Stairtower\Constants;
use Cundd\Stairtower\DataAccess\CoordinatorInterface;
use Cundd\Stairtower\Event\SharedEventEmitter;
use Cundd\Stairtower\Serializer\JsonSerializer;
use Cundd\Stairtower\Server\Dispatcher\CoreDispatcher;
use Cundd\Stairtower\Server\Dispatcher\ServerActionDispatcherInterface;
use Cundd\Stairtower\Server\Handler\HandlerResultInterface;
use Cundd\Stairtower\Server\ValueObject\RequestInfoFactory;
use Cundd\Stairtower\Server\ValueObject\RequestInterface;
use DI\Container;
use Psr\Log\LoggerInterface;
use React\EventLoop\LoopInterface;
use React\Http\Server as HttpServer;
use React\Socket\Server as SocketServer;

/**
 * HTTP Server
 */
class RestServer extends AbstractServer implements ServerActionDispatcherInterface
    //implements StandardActionDispatcherInterface, SpecialHandlerActionDispatcherInterface, ServerActionDispatcherInterface, ControllerActionDispatcherInterface
{
    /**
     * Listening socket server
     *
     * @var SocketServer
     */
    protected $socketServer;

    /**
     * Request factory instance to transform React requests into Stairtower requests
     *
     * @var \Cundd\Stairtower\Server\ValueObject\RequestInfoFactory
     * @Inject
     */
    protected $requestFactory;

    /**
     * @var CoreDispatcher
     */
    protected $coreDispatcher;

    /**
     * @var \Psr\Log\LoggerInterface
     * @inject
     */
    protected $logger;

    /**
     * Rest Server constructor
     *
     * @param Container            $container
     * @param LoggerInterface      $logger
     * @param LoopInterface        $eventLoop
     * @param SharedEventEmitter   $eventEmitter
     * @param CoordinatorInterface $coordinator
     * @param ResponseBuilder      $responseBuilder
     * @param JsonSerializer       $serializer
     * @param RequestInfoFactory   $requestFactory
     */
    public function __construct(
        Container $container,
        LoggerInterface $logger,
        LoopInterface $eventLoop,
        SharedEventEmitter $eventEmitter,
        CoordinatorInterface $coordinator,
        ResponseBuilder $responseBuilder,
        JsonSerializer $serializer,
        RequestInfoFactory $requestFactory
    ) {
        parent::__construct($container, $logger, $eventLoop, $eventEmitter, $coordinator, $serializer);
        $this->coreDispatcher = new CoreDispatcher($requestFactory, $responseBuilder, $container, $logger, $this);
    }

    public function stop()
    {
        $this->socketServer->close();
        parent::stop();
    }

    protected function setupServer()
    {
        $loop = $this->getEventLoop();

        $server = new HttpServer([$this->coreDispatcher, 'dispatch']);
        $this->socketServer = new SocketServer($this->getIp() . ':' . $this->getPort(), $loop);
        $server->listen($this->socketServer);

        $server->on(
            'error',
            function (\Throwable $e) {
                echo 'Error: ' . $e->getMessage() . PHP_EOL;
            }
        );

        $this->writeln(Constants::MESSAGE_CLI_WELCOME . PHP_EOL);
        $this->writeln('Start listening on %s:%s', $this->getIp(), $this->getPort());
        $this->logger->info(sprintf('Start listening on %s:%s', $this->getIp(), $this->getPort()));
    }

    public function dispatchServerAction(string $serverAction, RequestInterface $request): HandlerResultInterface
    {
        return $this->handleServerAction($serverAction);
    }
}
