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
use Cundd\PersistentObjectStore\Memory\Manager;
use Cundd\PersistentObjectStore\RuntimeException;
use Cundd\PersistentObjectStore\Server\Exception\InvalidEventLoopException;
use Cundd\PersistentObjectStore\Server\Exception\InvalidServerChangeException;
use Cundd\PersistentObjectStore\Server\Exception\ServerException;
use Cundd\PersistentObjectStore\Server\ValueObject\HandlerResult;
use Cundd\PersistentObjectStore\Server\ValueObject\Statistics;
use DateTime;
use React\EventLoop\Timer\TimerInterface;
use React\Http\Response;

/**
 * Abstract server
 *
 * @package Cundd\PersistentObjectStore
 */
abstract class AbstractServer implements ServerInterface {
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
	 * Event loop
	 *
	 * @var \React\EventLoop\LoopInterface
	 * @Inject
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
	 * Timer to schedule maintenance tasks
	 *
	 * @var TimerInterface
	 */
	protected $maintenanceTimer;

	/**
	 * The number of minimum seconds between maintenance tasks
	 *
	 * @var float
	 */
	protected $maintenanceInterval = 5.0;

	/**
	 * Create and configure the server objects
	 */
	abstract protected function setupServer();

	/**
	 * Starts the request loop
	 */
	public function start() {
		$this->prepareEventLoop();
		$this->setupServer();
		$this->startTime = new DateTime();
		$this->_isRunning = TRUE;
		$this->eventLoop->run();
		$this->_isRunning = FALSE;
	}

	/**
	 * Stops to listen for connections
	 */
	public function stop() {
		$this->getEventLoop()->stop();
		$this->_isRunning = FALSE;
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
	 * Total shutdown of the server
	 *
	 * Stops to listen for incoming connections, runs the maintenance task and terminates the programm
	 */
	public function shutdown() {
		$this->stop();
		$this->runMaintenance();
		$this->writeln('Server is going to shut down now');
	}

	/**
	 * Prepare the event loop
	 */
	public function prepareEventLoop() {
		$this->postponeMaintenance();
	}

	/**
	 * A maintenance task that will be performed when the server becomes idle
	 */
	public function runMaintenance() {
		$this->coordinator->commitDatabases();
		Manager::cleanup();
	}

	/**
	 * Postpone the maintenance run
	 */
	public function postponeMaintenance() {
		if ($this->maintenanceTimer) {
			$this->eventLoop->cancelTimer($this->maintenanceTimer);
		}
		$this->maintenanceTimer = $this->eventLoop->addTimer($this->maintenanceInterval, function($timer) {
			$this->runMaintenance();
			$this->postponeMaintenance();
		});
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
		$statistics = new Statistics(Constants::VERSION, $this->getGuid(), $this->getStartTime(), memory_get_usage(TRUE), memory_get_peak_usage(TRUE));
		if (!$detailed) {
			return $statistics;
		}

		$detailedStatistics = $statistics->jsonSerialize() + [
			'eventLoopImplementation' => get_class($this->getEventLoop()),
		];
		return $detailedStatistics;
	}

	/**
	 * Handles the given exception
	 *
	 * @param \Exception           $error
	 * @param \React\Http\Request $request
	 * @param \React\Http\Response $response
	 * @throws \Exception
	 */
	public function handleError($error, $request, Response $response) {
		$this->handleResult(new HandlerResult($this->getStatusCodeForException($error), $error->getMessage()), $request, $response);
		$this->writeln('Caught exception #%d: %s', $error->getCode(), $error->getMessage());
		$this->writeln($error->getTraceAsString());
	}

	/**
	 * Returns the status code that best describes the given error
	 *
	 * @param \Exception $error
	 * @return int
	 */
	public function getStatusCodeForException($error) {
		if (!$error || !($error instanceof \Exception)) {
			return 500;
		}
		switch (get_class($error)) {
			case 'Cundd\\PersistentObjectStore\\DataAccess\\Exception\\ReaderException': $statusCode = ($error->getCode() === 1408127629 ? 400 : 500); break;

			case 'Cundd\\PersistentObjectStore\\Domain\\Model\\Exception\\InvalidDatabaseException': $statusCode = 400; break;
			case 'Cundd\\PersistentObjectStore\\Domain\\Model\\Exception\\InvalidDatabaseIdentifierException': $statusCode = 400; break;
			case 'Cundd\\PersistentObjectStore\\Domain\\Model\\Exception\\InvalidDataException': $statusCode = 500; break;
			case 'Cundd\\PersistentObjectStore\\Domain\\Model\\Exception\\InvalidDataIdentifierException': $statusCode = 400; break;

			case 'Cundd\\PersistentObjectStore\\Server\\Exception\\InvalidBodyException': $statusCode = 400; break;
			case 'Cundd\\PersistentObjectStore\\Server\\Exception\\InvalidEventLoopException': $statusCode = 500; break;
			case 'Cundd\\PersistentObjectStore\\Server\\Exception\\InvalidRequestException': $statusCode = 400; break;
			case 'Cundd\\PersistentObjectStore\\Server\\Exception\\InvalidRequestMethodException': $statusCode = 405; break;
			case 'Cundd\\PersistentObjectStore\\Server\\Exception\\InvalidRequestParameterException': $statusCode = 400; break;
			case 'Cundd\\PersistentObjectStore\\Server\\Exception\\InvalidServerChangeException': $statusCode = 500; break;
			case 'Cundd\\PersistentObjectStore\\Server\\Exception\\MissingLengthHeaderException': $statusCode = 411; break;
			case 'Cundd\\PersistentObjectStore\\Server\\Exception\\ServerException': $statusCode = 500; break;

			case 'Cundd\\PersistentObjectStore\\Filter\\Exception\\InvalidCollectionException': $statusCode = 500; break;
			case 'Cundd\\PersistentObjectStore\\Filter\\Exception\\InvalidComparisonException': $statusCode = 500; break;
			case 'Cundd\\PersistentObjectStore\\Filter\\Exception\\InvalidOperatorException': $statusCode = 500; break;
			default: $statusCode = 500;
		}
		return $statusCode;
	}

	/**
	 * Handles the given server action
	 *
	 * @param string $serverAction
	 * @param \React\Http\Request  $request
	 * @param \React\Http\Response $response
	 */
	public function handleServerAction($serverAction, $request, $response) {
		switch ($serverAction) {
			case 'restart':
				if (!$this->isRunning()) throw new ServerException('Server is currently not running', 1413201286);
				$this->handleResult(new HandlerResult(202, 'Server is going to restart'), $request, $response);
				$this->restart();
				break;

			case 'shutdown':
				$this->handleResult(new HandlerResult(202, 'Server is going to shut down'), $request, $response);
				$this->eventLoop->addTimer(0.5, array($this, 'shutdown'));
				break;

//			case 'stop':
//				$this->handleResult(new HandlerResult(200, 'Server is going to stop'), $request, $response);
//				$this->stop();
////				$this->eventLoop->addTimer(0.5, array($this, 'shutdown'));
//				break;
		}
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
	 * Prints the given log message very fast
	 *
	 * @experimental
	 *
	 * @param string $format
	 * @param null $vars
	 * @throws RuntimeException
	 */
	protected function log($format, $vars = NULL) {
		if (func_num_args() > 1) {
			$arguments = func_get_args();
			array_shift($arguments);
			$writeData = vsprintf($format, $arguments);
		} else {
			$writeData = $format;
		}

		$writeData = gmdate('r') . ': ' . $writeData . PHP_EOL;

		$logFileDirectory = ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('logPath');
		$logFilePath = $logFileDirectory . 'log-' . getmypid() . '.log';

		if (!file_exists($logFileDirectory)) {
			mkdir($logFileDirectory);
		}
		$fileHandle = fopen($logFilePath, 'w');

		if (!$fileHandle) throw new RuntimeException(sprintf('Could not open file %s', $logFilePath), 1413294319);
		stream_set_blocking($fileHandle, 0);

		fwrite($fileHandle, $writeData);
		fclose($fileHandle);
	}


}