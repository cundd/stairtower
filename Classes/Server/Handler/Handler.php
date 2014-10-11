<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 10.10.14
 * Time: 17:59
 */
namespace Cundd\PersistentObjectStore\Server\Handler;

use Cundd\PersistentObjectStore\Domain\Model\DatabaseInterface;
use Cundd\PersistentObjectStore\Domain\Model\DataInterface;
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
	 * Creates a new Data instance with the given data for the given RequestInfo
	 *
	 * @param RequestInfo $requestInfo
	 * @param mixed       $data
	 * @return HandlerResultInterface
	 */
	public function create(RequestInfo $requestInfo, $data) {
		// TODO: Implement create() method.
	}

	/**
	 * Read Data instances for the given RequestInfo
	 *
	 * @param RequestInfo $requestInfo
	 * @return HandlerResultInterface
	 */
	public function read(RequestInfo $requestInfo) {
		if ($requestInfo->getDataIdentifier()) {
			return new HandlerResult(
				200,
				$this->getDataForRequest($requestInfo)
			);
		}
		$database = $this->getDatabaseForRequestInfo($requestInfo);
		if ($database) {

		}
		return new HandlerResult(200, $database);
	}

	/**
	 * Update a Data instance with the given data for the given RequestInfo
	 *
	 * @param RequestInfo $requestInfo
	 * @param mixed       $data
	 * @return HandlerResultInterface
	 */
	public function update(RequestInfo $requestInfo, $data) {
		// TODO: Implement update() method.
	}

	/**
	 * Deletes a Data instance for the given RequestInfo
	 *
	 * @param RequestInfo $requestInfo
	 * @return HandlerResultInterface
	 */
	public function delete(RequestInfo $requestInfo) {
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
		$this->getDatabaseForRequestInfo($requestInfo)->remove($dataInstance);
		return new HandlerResult(200);
	}

	/**
	 * Returns the database for the given request or NULL if it is not specified
	 *
	 * @param RequestInfo $requestInfo
	 * @return DatabaseInterface|NULL
	 */
	public function getDatabaseForRequestInfo(RequestInfo $requestInfo) {
		return $requestInfo->getDatabaseIdentifier() ? $this->coordinator->getDatabase($requestInfo->getDatabaseIdentifier()) : NULL;
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