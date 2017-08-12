<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server;

use Cundd\Stairtower\Configuration\ConfigurationManager;
use Cundd\Stairtower\Constants;
use Cundd\Stairtower\DataAccess\CoordinatorInterface;
use Cundd\Stairtower\Event\SharedEventEmitter;
use Cundd\Stairtower\Memory\Manager;
use Cundd\Stairtower\Serializer\JsonSerializer;
use Cundd\Stairtower\Server\Exception\InvalidEventLoopException;
use Cundd\Stairtower\Server\Exception\InvalidRequestException;
use Cundd\Stairtower\Server\Exception\InvalidServerChangeException;
use Cundd\Stairtower\Server\Exception\ServerException;
use Cundd\Stairtower\Server\ValueObject\HandlerResult;
use Cundd\Stairtower\Server\ValueObject\Statistics;
use Cundd\Stairtower\System\Lock\Factory;
use Cundd\Stairtower\System\Lock\TransientLock;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DI\Container;
use Psr\Log\LoggerInterface;
use React\EventLoop\LoopInterface;
use React\EventLoop\Timer\TimerInterface;

/**
 * Abstract server
 */
abstract class AbstractServer implements ServerInterface
{
    use OutputWriterTrait;

    /**
     * Document Access Coordinator
     *
     * @var CoordinatorInterface
     * @Inject
     */
    protected $coordinator;

    /**
     * JSON serializer
     *
     * @var JsonSerializer
     * @Inject
     */
    protected $serializer;

    /**
     * DI container
     *
     * @var Container
     * @Inject
     */
    protected $diContainer;

    /**
     * @var LoggerInterface
     * @Inject
     */
    protected $logger;

    /**
     * Port number to listen on
     *
     * @var int
     */
    private $port = Constants::SERVER_DEFAULT_PORT;

    /**
     * IP to listen on
     *
     * @var string
     */
    private $ip = Constants::SERVER_DEFAULT_IP;

    /**
     * Event loop
     *
     * @var \React\EventLoop\LoopInterface
     * @Inject
     */
    private $eventLoop;

    /**
     * Indicates if the server is currently running
     *
     * @var bool
     */
    private $isRunningFlag = false;

    /**
     * Servers start time
     *
     * @var DateTime
     */
    private $startTime;

    /**
     * Timer to schedule maintenance tasks
     *
     * @var TimerInterface
     */
    private $maintenanceTimer;

    /**
     * The number of minimum seconds between maintenance tasks
     *
     * @var float
     */
    private $maintenanceInterval = 50.0;

    /**
     * If run in test mode the server will stop after this number of seconds
     *
     * @var int
     */
    private $autoShutdownTime = 60;

    /**
     * Event Emitter
     *
     * @var SharedEventEmitter
     * @Inject
     */
    private $eventEmitter;

    /**
     * Mode in which the server is started
     *
     * @var int
     */
    private $mode = ServerInterface::SERVER_MODE_NOT_RUNNING;

    /**
     * Server constructor
     *
     * @param Container            $container
     * @param LoggerInterface      $logger
     * @param LoopInterface        $eventLoop
     * @param SharedEventEmitter   $eventEmitter
     * @param CoordinatorInterface $coordinator
     * @param JsonSerializer       $serializer
     */
    public function __construct(
        Container $container,
        LoggerInterface $logger,
        LoopInterface $eventLoop,
        SharedEventEmitter $eventEmitter,
        CoordinatorInterface $coordinator,
        JsonSerializer $serializer
    ) {
        $this->coordinator = $coordinator;
        $this->serializer = $serializer;
        $this->diContainer = $container;
        $this->logger = $logger;
        $this->eventLoop = $eventLoop;
        $this->eventEmitter = $eventEmitter;
    }

    public function collectStatistics(): Statistics
    {
        return new Statistics($this->getGuid(), $this->getStartTime(), get_class($this->getEventLoop()));
    }

    public function getGuid()
    {
        return sprintf(
            'stairtower_%s_%s_%s_%d',
            Constants::VERSION,
            getmypid(),
            $this->getIp(),
            $this->getPort()
        );
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setIp(string $ip): ServerInterface
    {
        if ($this->isRunningFlag) {
            throw new InvalidServerChangeException('Can not change IP when server is running', 1412956590);
        }
        $this->ip = $ip;

        return $this;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function setPort(int $port): ServerInterface
    {
        if ($this->isRunningFlag) {
            throw new InvalidServerChangeException('Can not change port when server is running', 1412956591);
        }
        $this->port = $port;

        return $this;
    }

    public function getStartTime(): ?DateTimeInterface
    {
        return $this->startTime;
    }


    /**
     * Handles the given server action
     *
     * @param string $serverAction
     * @return HandlerResult
     */
    public function handleServerAction(string $serverAction)
    {
        switch ($serverAction) {
            case 'restart':
                if (!$this->isRunning()) {
                    throw new ServerException('Server is currently not running', 1413201286);
                }
                $this->restart();

                return new HandlerResult(202, 'Server is going to restart');

            case 'shutdown':
                $this->eventLoop->addTimer(0.5, [$this, 'shutdown']);

                return new HandlerResult(202, 'Server is going to shut down');

//			case 'stop':
//				$this->handleResult(new HandlerResult(200, 'Server is going to stop'), $request, $response);
//				$this->stop();
////				$this->eventLoop->addTimer(0.5, array($this, 'shutdown'));
//				break;

            default:
                throw new InvalidRequestException(sprintf('Invalid server action %s', $serverAction));
        }
    }

    /**
     * Returns if the server is running
     *
     * @return bool
     */
    public function isRunning(): bool
    {
        return $this->isRunningFlag;
    }

    /**
     * Restarts the server
     */
    public function restart()
    {
        if (!$this->isRunning()) {
            throw new ServerException('Server is currently not running', 1413201286);
        }
        $this->stop();
        $this->start();
    }

    /**
     * Starts the request loop
     */
    public function start()
    {
        $this->prepareEventLoop();
        $this->setupServer();
        $this->startTime = new DateTimeImmutable();
        $this->isRunningFlag = true;
        $this->getEventLoop()->run();
        $this->isRunningFlag = false;
    }

    /**
     * Prepare the event loop
     */
    public function prepareEventLoop()
    {
        $this->postponeMaintenance();


        // If the server is run in test-mode shut it down after 1 minute
        if ($this->getMode() === ServerInterface::SERVER_MODE_TEST) {
            $this->writeln(
                'Server is started in test mode and will shut down after %d seconds',
                $this->getAutoShutdownTime()
            );
            $this->eventLoop->addTimer(
                $this->getAutoShutdownTime(),
                function () {
                    $this->writeln('Auto shutdown time reached');
                    $this->shutdown();
                }
            );
        }
    }

    /**
     * Postpone the maintenance run
     */
    public function postponeMaintenance()
    {
        if ($this->maintenanceTimer) {
            $this->eventLoop->cancelTimer($this->maintenanceTimer);
        }
        $this->maintenanceTimer = $this->eventLoop->addTimer(
            $this->maintenanceInterval,
            function () {
                $this->runMaintenance();
                $this->postponeMaintenance();
            }
        );
    }

    /**
     * Returns the mode of the server
     *
     * @return int
     */
    public function getMode(): int
    {
        return $this->mode;
    }

    /**
     * Sets the mode of the server
     *
     * @param int $mode
     * @return ServerInterface
     */
    public function setMode(int $mode): ServerInterface
    {
        if ($this->isRunningFlag) {
            throw new InvalidServerChangeException('Can not change the mode when server is running', 1414835788);
        }
        if (false === (
                $mode === self::SERVER_MODE_NORMAL
                || $mode === self::SERVER_MODE_NOT_RUNNING
                || $mode === self::SERVER_MODE_TEST
                || $mode === self::SERVER_MODE_DEVELOPMENT
            )
        ) {
            throw new ServerException(sprintf('Invalid server mode %s', $mode), 1421096002);
        }
        $this->mode = $mode;
        ConfigurationManager::getSharedInstance()->setConfigurationForKeyPath('serverMode', $mode);
        Factory::setLockImplementationClass(TransientLock::class);

        return $this;
    }

    /**
     * Returns the number of seconds after which to stop the server if run in test mode
     *
     * @return int
     */
    public function getAutoShutdownTime(): int
    {
        return $this->autoShutdownTime;
    }

    /**
     * Sets the number of seconds after which to stop the server if run in test mode
     *
     * @param int $autoShutdownTime
     * @return ServerInterface
     */
    public function setAutoShutdownTime(int $autoShutdownTime): ServerInterface
    {
        $this->autoShutdownTime = $autoShutdownTime;

        return $this;
    }

    /**
     * Total shutdown of the server
     *
     * Stops to listen for incoming connections, runs the maintenance task and terminates the program
     */
    public function shutdown()
    {
        $this->stop();
        $this->runMaintenance();
        $this->writeln('Server is going to shut down now');
    }

    /**
     * Stops to listen for connections
     */
    public function stop()
    {
        $this->getEventLoop()->stop();
        $this->isRunningFlag = false;
    }

    /**
     * Returns the event loop instance
     *
     * @return LoopInterface if the event loop is not set
     */
    public function getEventLoop(): LoopInterface
    {
        if (!$this->eventLoop) {
            throw new InvalidEventLoopException('Event loop not set', 1412942824);
        }

        return $this->eventLoop;
    }

    /**
     * Sets the event loop
     *
     * @param LoopInterface|\React\EventLoop\LoopInterface $eventLoop
     * @return ServerInterface
     */
    public function setEventLoop(LoopInterface $eventLoop): ServerInterface
    {
        if ($this->isRunningFlag) {
            throw new InvalidServerChangeException('Can not change the event loop when server is running', 1412956592);
        }
        $this->eventLoop = $eventLoop;

        return $this;
    }

    /**
     * A maintenance task that will be performed when the server becomes idle
     */
    public function runMaintenance()
    {
        $this->logger->debug('Run maintenance');
        $this->eventEmitter->emit(Event::MAINTENANCE);
        $this->coordinator->commitDatabases();
        Manager::cleanup();
        $this->logger->debug('Finished maintenance');
    }

    /**
     * Prints the given log message very fast
     *
     * @experimental
     *
     * @param string $format
     * @param array  ...$vars
     */
    protected function log(string $format, ...$vars)
    {
        if (!empty($vars)) {
            $writeData = vsprintf($format, ...$vars);
        } else {
            $writeData = $format;
        }
        $this->logger->info($writeData);
    }

    /**
     * Create and configure the server objects
     */
    abstract protected function setupServer();
}
