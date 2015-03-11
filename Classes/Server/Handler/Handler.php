<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 10.10.14
 * Time: 17:59
 */
namespace Cundd\PersistentObjectStore\Server\Handler;

use Cundd\PersistentObjectStore\Constants;
use Cundd\PersistentObjectStore\DataAccess\Exception\ReaderException;
use Cundd\PersistentObjectStore\Domain\Model\DatabaseInterface;
use Cundd\PersistentObjectStore\Domain\Model\Document;
use Cundd\PersistentObjectStore\Domain\Model\DocumentInterface;
use Cundd\PersistentObjectStore\Expand\ExpandConfigurationInterface;
use Cundd\PersistentObjectStore\Filter\FilterResultInterface;
use Cundd\PersistentObjectStore\Server\Exception\InvalidBodyException;
use Cundd\PersistentObjectStore\Server\Exception\InvalidRequestParameterException;
use Cundd\PersistentObjectStore\Server\ValueObject\HandlerResult;
use Cundd\PersistentObjectStore\Server\ValueObject\RequestInfo;
use Cundd\PersistentObjectStore\Utility\DebugUtility;
use SplFixedArray;

/**
 * Handler implementation
 *
 * @package Cundd\PersistentObjectStore\Server\Handler
 */
class Handler implements HandlerInterface
{
    /**
     * Document Access Coordinator
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
     * ExpandConfigurationBuilder instance
     *
     * @var \Cundd\PersistentObjectStore\Expand\ExpandConfigurationBuilderInterface
     * @Inject
     */
    protected $expandConfigurationBuilder;

    /**
     * Property descriptor instance
     *
     * @var \Cundd\PersistentObjectStore\Meta\Database\Property\Descriptor
     * @Inject
     */
    protected $propertyDescriptor;

    /**
     * Expand Resolver instance
     *
     * @var \Cundd\PersistentObjectStore\Expand\ExpandResolverInterface
     * @Inject
     */
    protected $expandResolver;

    // 	 * @var \Evenement\EventEmitterInterface

    /**
     * Event Emitter
     *
     * @var \Cundd\PersistentObjectStore\Event\SharedEventEmitter
     * @Inject
     */
    protected $eventEmitter;

    /**
     * Invoked if no route is given (e.g. if the request path is empty)
     *
     * @param RequestInfo $requestInfo
     * @return HandlerResultInterface
     */
    public function noRoute(RequestInfo $requestInfo)
    {
        return new HandlerResult(200, Constants::MESSAGE_JSON_WELCOME);
    }


    /**
     * Creates a new Document instance or Database with the given data for the given RequestInfo
     *
     * @param RequestInfo $requestInfo
     * @param mixed       $data
     * @return HandlerResultInterface
     */
    public function create(RequestInfo $requestInfo, $data)
    {
        if ($requestInfo->getMethod() === 'POST') { // Create a Document instance
            return $this->createDataInstance($requestInfo, $data);
        }

        if ($requestInfo->getMethod() === 'PUT') { // Create a Database
            return $this->createDatabase($requestInfo, $data);
        }
        return new HandlerResult(400, sprintf('Invalid HTTP method %s', $requestInfo->getMethod()));
    }

    /**
     * Creates and returns a new Document instance
     *
     * @param RequestInfo $requestInfo
     * @param mixed       $data
     * @return HandlerResult
     */
    protected function createDataInstance(RequestInfo $requestInfo, $data)
    {
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

        $document = new Document($data);

        if ($requestInfo->getDataIdentifier()) {
            throw new InvalidRequestParameterException(
                'Document identifier in request path is not allowed when creating a Document instance. Use PUT to update',
                1413278767
            );
        }
        if ($database->contains($document)) {
            throw new InvalidBodyException(
                sprintf(
                    'Database \'%s\' already contains the given data. Maybe the values of the identifier are not expressive',
                    $database->getIdentifier()
                ),
                1413215990
            );
        }

        $database->add($document);
        $this->eventEmitter->scheduleFutureEmit(Event::DOCUMENT_CREATED, array($document));
        return new HandlerResult(
            201,
            $document
        );
    }

    /**
     * Returns the database for the given request or NULL if it is not specified
     *
     * @param RequestInfo $requestInfo
     * @return DatabaseInterface|NULL
     */
    public function getDatabaseForRequestInfo(RequestInfo $requestInfo)
    {
        if (!$requestInfo->getDatabaseIdentifier()) {
            return null;
        }
        $databaseIdentifier = $requestInfo->getDatabaseIdentifier();
        try {
            return $this->coordinator->getDatabase($databaseIdentifier);
        } catch (ReaderException $exception) {
            return null;
        }
    }

    /**
     * Creates and returns a new Database
     *
     * @param RequestInfo $requestInfo
     * @param mixed       $data
     * @return HandlerResult
     */
    protected function createDatabase(RequestInfo $requestInfo, $data)
    {
        if ($requestInfo->getDataIdentifier()) {
            throw new InvalidRequestParameterException(
                'Document identifier in request path is not allowed when creating a Database',
                1413278767
            );
        }

        if ($data) {
            DebugUtility::pl('Database creation parameters are currently not supported');
        }
        $databaseIdentifier = $requestInfo->getDatabaseIdentifier();
        $database           = $this->coordinator->createDatabase($databaseIdentifier);
        if ($database) {
            $this->eventEmitter->scheduleFutureEmit(Event::DATABASE_CREATED, array($database));
            return new HandlerResult(201, sprintf('Database "%s" created', $databaseIdentifier));
        } else {
            return new HandlerResult(400);
        }
    }

    /**
     * Read Document instances for the given RequestInfo
     *
     * @param RequestInfo $requestInfo
     * @return HandlerResultInterface
     */
    public function read(RequestInfo $requestInfo)
    {
        $query = $requestInfo->getRequest()->getQuery();

        // Extract the Expand configuration from the query
        $expandConfiguration = null;
        if (isset($query[Constants::EXPAND_KEYWORD]) && $query[Constants::EXPAND_KEYWORD]) {
            $expandConfiguration = $this->expandConfigurationBuilder->buildExpandConfigurations($query[Constants::EXPAND_KEYWORD]);
            unset($query[Constants::EXPAND_KEYWORD]);
        }

        // If a Data identifier load and return the Document instance
        if ($requestInfo->getDataIdentifier()) {
            $document = $this->getDataForRequest($requestInfo);
            if ($document) {
                if ($expandConfiguration) {
                    $collection = $this->expandObjectsInCollection(array($document), $expandConfiguration);
                    $document   = $collection[0];
                }

                return new HandlerResult(
                    200,
                    $document
                );
            } else {
                return new HandlerResult(
                    404,
                    sprintf(
                        'Document instance with identifier "%s" not found in database "%s"',
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


        // If there is a filter defined
        if ($query) {
            $filter            = $this->filterBuilder->buildFilter($query);
            $filterResult      = $filter->filterCollection($database);
            $filterResultCount = $filterResult->count();
            $statusCode        = $filterResultCount > 0 ? 200 : 404;
            if (!$expandConfiguration || $filterResultCount === 0) {
                return new HandlerResult($statusCode, $filterResult);
            }

            $collection = $this->expandObjectsInCollection($filterResult->toFixedArray(), $expandConfiguration);
            return new HandlerResult($statusCode, $collection);
        }

        // If there is no filter but an Expand configuration
        if ($expandConfiguration) {
            $collection = $this->expandObjectsInCollection($database->toFixedArray(), $expandConfiguration);
            return new HandlerResult(200, $collection);
        }
        return new HandlerResult(200, $database);
    }

    /**
     * Returns the Document for the given request or NULL if it is not specified
     *
     * @param RequestInfo $requestInfo
     * @return DocumentInterface|NULL
     */
    public function getDataForRequest(RequestInfo $requestInfo)
    {
        if (!$requestInfo->getDataIdentifier()) {
            return null;
        }
        $database = $this->getDatabaseForRequestInfo($requestInfo);
        return $database ? $database->findByIdentifier($requestInfo->getDataIdentifier()) : null;
    }

    /**
     * Update a Document instance with the given data for the given RequestInfo
     *
     * @param RequestInfo $requestInfo
     * @param mixed       $data
     * @return HandlerResultInterface
     */
    public function update(RequestInfo $requestInfo, $data)
    {
        if (!$requestInfo->getDataIdentifier()) {
            throw new InvalidRequestParameterException('Document identifier is missing', 1413292389);
        }
        $document = $this->getDataForRequest($requestInfo);
        if (!$document) {
            return new HandlerResult(404, sprintf(
                'Document instance with identifier "%s" not found in database "%s"',
                $requestInfo->getDataIdentifier(),
                $requestInfo->getDatabaseIdentifier()
            ));
        }

        $database = $this->getDatabaseForRequestInfo($requestInfo);

        $data[Constants::DATA_ID_KEY] = $requestInfo->getDataIdentifier();
        $newDocument                  = new Document($data, $database->getIdentifier());
        $database->update($newDocument);
        $this->eventEmitter->scheduleFutureEmit(Event::DOCUMENT_UPDATED, array($document));
        return new HandlerResult(200, $newDocument);
    }

    /**
     * Deletes a Document instance for the given RequestInfo
     *
     * @param RequestInfo $requestInfo
     * @return HandlerResultInterface
     */
    public function delete(RequestInfo $requestInfo)
    {
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


//		if (!$requestInfo->getDataIdentifier()) throw new InvalidRequestParameterException('Document identifier is missing', 1413035855);
        if ($requestInfo->getDataIdentifier()) {
            $document = $this->getDataForRequest($requestInfo);
            if (!$document) {
                throw new InvalidRequestParameterException(
                    sprintf(
                        'Document with identifier "%s" not found in database "%s"',
                        $requestInfo->getDataIdentifier(),
                        $requestInfo->getDatabaseIdentifier()
                    ),
                    1413035855
                );
            }
            $database->remove($document);

            $this->eventEmitter->scheduleFutureEmit(Event::DOCUMENT_DELETED, array($document));
            return new HandlerResult(204, sprintf('Document "%s" deleted', $requestInfo->getDataIdentifier()));
        }

        $databaseIdentifier = $database->getIdentifier();
        $this->coordinator->dropDatabase($databaseIdentifier);
        $this->eventEmitter->scheduleFutureEmit(Event::DATABASE_DELETED, array($database));
        return new HandlerResult(204, sprintf('Database "%s" deleted', $databaseIdentifier));
    }

    /**
     * Action to display server statistics
     *
     * @param RequestInfo $requestInfo
     * @return HandlerResultInterface
     */
    public function getStatsAction(RequestInfo $requestInfo)
    {
        $detailedStatistics = $requestInfo->getDataIdentifier() === 'detailed';
        return new HandlerResult(200, $this->server->collectStatistics($detailedStatistics));
    }

    /**
     * Action to display all databases
     *
     * @param RequestInfo $requestInfo
     * @return HandlerResultInterface
     */
    public function getAllDbsAction(RequestInfo $requestInfo)
    {
        return new HandlerResult(200, $this->coordinator->listDatabases());
    }

    /**
     * Returns the count of the result set
     *
     * @param RequestInfo $requestInfo
     * @return HandlerResultInterface
     */
    public function getCountAction(RequestInfo $requestInfo)
    {
        $count      = null;
        $readResult = $this->read($requestInfo);
        $data       = $readResult->getData();
        if ($data instanceof DatabaseInterface || $data instanceof FilterResultInterface) {
            $count = $data->count();
        }
        if ($count === null) {
            return new HandlerResult(400, sprintf(
                'Could not count result of type %s',
                (is_object($data) ? get_class($data) : gettype($data))
            ));
        }
        return new HandlerResult(200, array('count' => $count));
    }

    /**
     * Returns the description of a database
     *
     * @param RequestInfo $requestInfo
     * @return HandlerResultInterface
     */
    public function getDescribeAction(RequestInfo $requestInfo)
    {
        if (!$requestInfo->getDatabaseIdentifier()) {
            return new HandlerResult(400, 'Missing database identifier');
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
        $propertyDescription = $this->propertyDescriptor->describe($database);

        return new HandlerResult(200, $propertyDescription);
    }

    /**
     * Expand the objects in the given collection according to the given Expand configurations
     *
     * @param SplFixedArray|DocumentInterface[]  $collection
     * @param ExpandConfigurationInterface[]     $expandConfigurationCollection
     * @return SplFixedArray
     */
    protected function expandObjectsInCollection($collection, $expandConfigurationCollection)
    {
        if (is_array($collection)) {
            $collectionCount = count($collection);
        } else {
            $collectionCount = $collection->getSize();
        }

        if ($collectionCount === 0) {
            return new SplFixedArray(0);
        }

        if ($collectionCount < 2) {
            $item = clone $collection[0];
            foreach ($expandConfigurationCollection as $expandConfiguration) {
                $this->expandResolver->expandDocument($item, $expandConfiguration);
            }
            return SplFixedArray::fromArray(array($item));
        }

        $clonedObjectCollection = new SplFixedArray($collectionCount);
        $i                      = 0;
        while ($i < $collectionCount) {
            $clonedObjectCollection[$i] = clone $collection[$i];
            $i++;
        }

        foreach ($expandConfigurationCollection as $expandConfiguration) {
            $this->expandResolver->expandDocumentCollection($clonedObjectCollection, $expandConfiguration);
        }
        return $clonedObjectCollection;
    }
}
