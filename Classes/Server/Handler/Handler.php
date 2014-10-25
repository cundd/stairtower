<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 10.10.14
 * Time: 17:59
 */
namespace Cundd\PersistentObjectStore\Server\Handler;

use Cundd\PersistentObjectStore\Constants;
use Cundd\PersistentObjectStore\Domain\Model\Data;
use Cundd\PersistentObjectStore\Domain\Model\DatabaseInterface;
use Cundd\PersistentObjectStore\Domain\Model\DataInterface;
use Cundd\PersistentObjectStore\Server\Exception\InvalidBodyException;
use Cundd\PersistentObjectStore\Server\Exception\InvalidRequestParameterException;
use Cundd\PersistentObjectStore\Server\ValueObject\HandlerResult;
use Cundd\PersistentObjectStore\Server\ValueObject\RequestInfo;

/**
 * Handler implementation
 *
 * @package Cundd\PersistentObjectStore\Server\Handler
 */
class Handler implements HandlerInterface {
	/**
	 * Data Access Coordinator
	 *
	 * @var \Cundd\PersistentObjectStore\DataAccess\CoordinatorInterface
	 * @Inject
	 */
	protected $coordinator;

	/**
	 * Server instance
	 *
	 * @var \Cundd\PersistentObjectStore\Server\ServerInterface
	 * @Inject
	 */
	protected $server;

	/**
	 * FilterBuilder instance
	 *
	 * @var \Cundd\PersistentObjectStore\Filter\FilterBuilderInterface
	 * @Inject
	 */
	protected $filterBuilder;

	/**
	 * Event Emitter
	 *
	 * @var \Evenement\EventEmitterInterface
	 * @Inject
	 */
	protected $eventEmitter;

	/**
	 * Invoked if no route is given (e.g. if the request path is empty)
	 *
	 * @param RequestInfo $requestInfo
	 * @return HandlerResultInterface
	 */
	public function noRoute(RequestInfo $requestInfo) {
		return new HandlerResult(200, Constants::MESSAGE_JSON_WELCOME);
	}


	/**
	 * Creates a new Data instance with the given data for the given RequestInfo
	 *
	 * @param RequestInfo $requestInfo
	 * @param mixed       $data
	 * @return HandlerResultInterface
	 */
	public function create(RequestInfo $requestInfo, $data) {
		$database     = $this->getDatabaseForRequestInfo($requestInfo);
		$dataInstance = new Data($data);

		if ($requestInfo->getDataIdentifier()) throw new InvalidRequestParameterException(
			'Data identifier in request path is not allowed when creating a Data instance. Use PUT to update',
			1413278767
		);
		if ($database->contains($dataInstance)) throw new InvalidBodyException(
			sprintf(
				'Database \'%s\' already contains the given data. Maybe the values of the identifier \'%s\' are not expressive',
				$database->getIdentifier(),
				$dataInstance->getIdentifierKey()
			),
			1413215990
		);

		$database->add($dataInstance);
		if ($database->contains($dataInstance)) {
			$this->eventEmitter->emit(Event::DOCUMENT_CREATED, array($dataInstance));
			return new HandlerResult(
				201,
				$dataInstance
			);
		} else {
			return new HandlerResult(400);
		}
	}

	/**
	 * Read Data instances for the given RequestInfo
	 *
	 * @param RequestInfo $requestInfo
	 * @return HandlerResultInterface
	 */
	public function read(RequestInfo $requestInfo) {
		if ($requestInfo->getDataIdentifier()) { // Load Data instance
			$dataInstance = $this->getDataForRequest($requestInfo);
			if ($dataInstance) {
				return new HandlerResult(
					200,
					$dataInstance
				);
			} else {
				return new HandlerResult(
					404,
					sprintf(
						'Data instance with identifier "%s" not found in database "%s"',
						$requestInfo->getDataIdentifier(),
						$requestInfo->getDatabaseIdentifier()
					)
				);
			}
		}

		$database = $this->getDatabaseForRequestInfo($requestInfo);
		if (!$database) {
			return new HandlerResult(
				404,
				sprintf(
					'Database with identifier "%s" not found',
					$requestInfo->getDatabaseIdentifier()
				)
			);
		}

		if (!$requestInfo->getRequest()->getQuery()) {
			return new HandlerResult(200, $database);
		}

		$filterResult = $this->filterBuilder->buildFilterFromQueryParts($requestInfo->getRequest()->getQuery(), $database);
		$statusCode   = $filterResult->count() > 0 ? 200 : 404;
		return new HandlerResult($statusCode, $filterResult);
	}

	/**
	 * Update a Data instance with the given data for the given RequestInfo
	 *
	 * @param RequestInfo $requestInfo
	 * @param mixed       $data
	 * @return HandlerResultInterface
	 */
	public function update(RequestInfo $requestInfo, $data) {
		if (!$requestInfo->getDataIdentifier()) throw new InvalidRequestParameterException('Data identifier is missing', 1413292389);
		$dataInstance = $this->getDataForRequest($requestInfo);
		if (!$dataInstance) {
			return new HandlerResult(404, sprintf(
				'Data instance with identifier "%s" not found in database "%s"',
				$requestInfo->getDataIdentifier(),
				$requestInfo->getDatabaseIdentifier()
			));
		}

		$database = $this->getDatabaseForRequestInfo($requestInfo);

		$newDataInstance = new Data($data, $database->getIdentifier(), $dataInstance->getIdentifierKey());
		$database->update($newDataInstance);
		$this->eventEmitter->emit(Event::DOCUMENT_UPDATED, array($dataInstance));
		return new HandlerResult(200, $newDataInstance);
	}

	/**
	 * Deletes a Data instance for the given RequestInfo
	 *
	 * @param RequestInfo $requestInfo
	 * @return HandlerResultInterface
	 */
	public function delete(RequestInfo $requestInfo) {
		$database = $this->getDatabaseForRequestInfo($requestInfo);
		if (!$database) {
			throw new InvalidRequestParameterException(
				sprintf(
					'Database with identifier "%s" not found',
					$requestInfo->getDatabaseIdentifier()
				),
				1413035859
			);
		}

		if (!$requestInfo->getDataIdentifier()) throw new InvalidRequestParameterException('Data identifier is missing', 1413035855);
		$dataInstance = $this->getDataForRequest($requestInfo);
		if (!$dataInstance) {
			throw new InvalidRequestParameterException(
				sprintf(
					'Data with identifier "%s" not found in database "%s"',
					$requestInfo->getDataIdentifier(),
					$requestInfo->getDatabaseIdentifier()
				),
				1413035855
			);
		}

		$database->remove($dataInstance);
		$this->eventEmitter->emit(Event::DOCUMENT_DELETED, array($dataInstance));
		return new HandlerResult(204);
	}

	/**
	 * Action to display server statistics
	 *
	 * @param RequestInfo $requestInfo
	 * @return HandlerResultInterface
	 */
	public function getStatsAction(RequestInfo $requestInfo) {
		$detailedStatistics = $requestInfo->getDataIdentifier() === 'detailed';
		return new HandlerResult(200, $this->server->collectStatistics($detailedStatistics));
	}

	/**
	 * Action to display all databases
	 *
	 * @param RequestInfo $requestInfo
	 * @return HandlerResultInterface
	 */
	public function getAllDbsAction(RequestInfo $requestInfo) {
		return new HandlerResult(200, $this->coordinator->listDatabases());
	}

	/**
	 * Returns the database for the given request or NULL if it is not specified
	 *
	 * @param RequestInfo $requestInfo
	 * @return DatabaseInterface|NULL
	 */
	public function getDatabaseForRequestInfo(RequestInfo $requestInfo) {
		if (!$requestInfo->getDatabaseIdentifier()) {
			return NULL;
		}
		$databaseIdentifier = $requestInfo->getDatabaseIdentifier();
		if (!$this->coordinator->databaseExists($databaseIdentifier)) {
			return NULL;
		}
		return $this->coordinator->getDatabase($databaseIdentifier);
	}

	/**
	 * Returns the data instance for the given request or NULL if it is not specified
	 *
	 * @param RequestInfo $requestInfo
	 * @return DataInterface|NULL
	 */
	public function getDataForRequest(RequestInfo $requestInfo) {
		if (!$requestInfo->getDataIdentifier()) {
			return NULL;
		}
		$database = $this->getDatabaseForRequestInfo($requestInfo);
		return $database ? $database->findByIdentifier($requestInfo->getDataIdentifier()) : NULL;
	}


} 