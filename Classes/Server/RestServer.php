<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 15.08.14
 * Time: 19:38
 */

namespace Cundd\PersistentObjectStore\Server;

use Cundd\PersistentObjectStore\Constants;
use Cundd\PersistentObjectStore\DataAccess\Coordinator;
use Cundd\PersistentObjectStore\Domain\Model\Data;
use Cundd\PersistentObjectStore\Domain\Model\DatabaseInterface;
use Cundd\PersistentObjectStore\Domain\Model\DataInterface;
use Cundd\PersistentObjectStore\Driver\Connection;
use Cundd\PersistentObjectStore\Driver\Driver;
use Cundd\PersistentObjectStore\Serializer\JsonSerializer;
use Cundd\PersistentObjectStore\Server\Exception\InvalidEventLoopException;
use Cundd\PersistentObjectStore\Server\ValueObject\PathInfoFactory;
use Cundd\PersistentObjectStore\Utility\DebugUtility;
use Cundd\PersistentObjectStore\Utility\GeneralUtility;
use Doctrine\DBAL\DriverManager;
use React\Http\Server as HttpServer;
use \React\Http\Request;
use React\Socket\Server as SocketServer;

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
	 * Handle the given request
	 *
	 * @param \React\Http\Request  $request
	 * @param \React\Http\Response $response
	 */
	public function handle($request, $response) {
		$response->writeHead(200, array('Content-Type' => 'application/json'));


		try {
			$pathInfo = $this->getPathPartsForRequest($request);

			if ($pathInfo->getDataIdentifier()) {
				$response->write("Give me object ID {$pathInfo->getDataIdentifier()} from database {$pathInfo->getDatabaseIdentifier()}\n");

				DebugUtility::var_dump($this->getDataForRequest($request));

				$response->end($this->formatter->format($this->getDataForRequest($request)));
			} else if ($pathInfo->getDatabaseIdentifier()) {
				$response->write("Give me database {$pathInfo->getDatabaseIdentifier()} \n");

				$response->end($this->formatter->format($this->getDatabaseForRequest($request)));
			} else {
				$response->end("Hallo World\n");
			}
		} catch (\Exception $exception) {
			$this->handleError($exception);
		}


//		if ($request->getPath() === '/') {
//		} else {
//
//
//			$databaseIdentifier = $input->getArgument('database');
//			return
//			$objectIdentifier = $input->getArgument('identifier');
//			GeneralUtility::assertDataIdentifier($objectIdentifier);
//			$database = $this->findDatabaseInstanceFromInput($input);
//			$dataInstance = $database->findByIdentifier($objectIdentifier);
//			if (!$dataInstance && !$graceful) throw new InvalidDataException(sprintf('Object with ID "%s" not found in database %s', $objectIdentifier, $database->getIdentifier()));
//			return $dataInstance;
//
//		}

//		$coordinator = $this->coordinator;
//		$serializer = $this->serializer;
//		$formatter = $this->formatter;
	}

	/**
	 * Starts the request loop
	 */
	public function start() {
		$socketServer = new SocketServer($this->getEventLoop());
		$httpServer   = new HttpServer($socketServer, $this->getEventLoop());
		$httpServer->on('request', array($this, 'handle'));
		$socketServer->listen($this->port, $this->ip);

		$this->writeln(Constants::MESSAGE_WELCOME . PHP_EOL);
		$this->writeln('Start listening on %s:%s', $this->ip, $this->port);

		$this->eventLoop->run();
	}

	/**
	 * Returns the PathInfo for the given request
	 *
	 * @param Request $request
	 * @return \Cundd\PersistentObjectStore\Server\ValueObject\PathInfo
	 */
	public function getPathPartsForRequest(Request $request) {
		return PathInfoFactory::buildPathInfoFromPath($request->getPath());
	}

	/**
	 * Returns the database for the given request or NULL if it is not specified
	 *
	 * @param Request $request
	 * @return DatabaseInterface|NULL
	 */
	public function getDatabaseForRequest(Request $request) {
		$pathInfo = $this->getPathPartsForRequest($request);
		return $pathInfo->getDatabaseIdentifier() ? $this->coordinator->getDatabase($pathInfo->getDatabaseIdentifier()) : NULL;
	}

	/**
	 * Returns the data instance for the given request or NULL if it is not specified
	 *
	 * @param Request $request
	 * @return DataInterface|NULL
	 */
	public function getDataForRequest(Request $request) {
		$pathInfo = $this->getPathPartsForRequest($request);
		if (!$pathInfo->getDataIdentifier()) {
			return NULL;
		}
		$database = $this->getDatabaseForRequest($request);
		return $database ? $database->findByIdentifier($pathInfo->getDataIdentifier()) : NULL;
	}
}