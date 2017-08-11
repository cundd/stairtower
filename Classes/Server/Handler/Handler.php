<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server\Handler;

use Cundd\Stairtower\Asset\AssetInterface;
use Cundd\Stairtower\Asset\AssetProviderInterface;
use Cundd\Stairtower\Constants;
use Cundd\Stairtower\DataAccess\CoordinatorInterface;
use Cundd\Stairtower\DataAccess\Exception\ReaderException;
use Cundd\Stairtower\Domain\Model\DatabaseInterface;
use Cundd\Stairtower\Domain\Model\Document;
use Cundd\Stairtower\Domain\Model\DocumentInterface;
use Cundd\Stairtower\Event\SharedEventEmitter;
use Cundd\Stairtower\Expand\ExpandConfigurationBuilderInterface;
use Cundd\Stairtower\Expand\ExpandConfigurationInterface;
use Cundd\Stairtower\Expand\ExpandResolverInterface;
use Cundd\Stairtower\Filter\FilterBuilderInterface;
use Cundd\Stairtower\Filter\FilterResultInterface;
use Cundd\Stairtower\Server\Exception\InvalidBodyException;
use Cundd\Stairtower\Server\Exception\InvalidRequestParameterException;
use Cundd\Stairtower\Server\ServerInterface;
use Cundd\Stairtower\Server\ValueObject\HandlerResult;
use Cundd\Stairtower\Server\ValueObject\NotFoundResult;
use Cundd\Stairtower\Server\ValueObject\RawResult;
use Cundd\Stairtower\Server\ValueObject\RequestInterface;
use Cundd\Stairtower\Utility\DebugUtility;
use SplFixedArray;

/**
 * Handler implementation
 */
class Handler implements HandlerInterface
{
    const HTTP_STATUS_CODE_DELETED = 202;

    /**
     * Document Access Coordinator
     *
     * @var CoordinatorInterface
     * @Inject
     */
    protected $coordinator;

    /**
     * Server instance
     *
     * @var ServerInterface
     * @Inject
     */
    protected $server;

    /**
     * FilterBuilder instance
     *
     * @var FilterBuilderInterface
     * @Inject
     */
    protected $filterBuilder;

    /**
     * ExpandConfigurationBuilder instance
     *
     * @var ExpandConfigurationBuilderInterface
     * @Inject
     */
    protected $expandConfigurationBuilder;

    /**
     * Expand Resolver instance
     *
     * @var ExpandResolverInterface
     * @Inject
     */
    protected $expandResolver;

    /**
     * Asset Loader instance
     *
     * @var AssetProviderInterface
     * @Inject
     */
    protected $assetLoader;

    /**
     * Event Emitter
     *
     * @var SharedEventEmitter
     * @Inject
     */
    protected $eventEmitter;

    /**
     * Handler constructor.
     *
     * @param ServerInterface                     $server
     * @param SharedEventEmitter                  $eventEmitter
     * @param CoordinatorInterface                $coordinator
     * @param FilterBuilderInterface              $filterBuilder
     * @param ExpandConfigurationBuilderInterface $expandConfigurationBuilder
     * @param ExpandResolverInterface             $expandResolver
     * @param AssetProviderInterface              $assetLoader
     */
    public function __construct(
        ServerInterface $server,
        SharedEventEmitter $eventEmitter,
        CoordinatorInterface $coordinator,
        FilterBuilderInterface $filterBuilder,
        ExpandConfigurationBuilderInterface $expandConfigurationBuilder,
        ExpandResolverInterface $expandResolver,
        AssetProviderInterface $assetLoader
    ) {
        $this->coordinator = $coordinator;
        $this->server = $server;
        $this->filterBuilder = $filterBuilder;
        $this->expandConfigurationBuilder = $expandConfigurationBuilder;
        $this->expandResolver = $expandResolver;
        $this->assetLoader = $assetLoader;
        $this->eventEmitter = $eventEmitter;
    }


    public function noRoute(RequestInterface $request): HandlerResultInterface
    {
        return new HandlerResult(200, Constants::MESSAGE_JSON_WELCOME);
    }

    public function create(RequestInterface $request, $data): HandlerResultInterface
    {
        if ($request->getMethod() === 'POST') { // Create a Document instance
            return $this->createDataInstance($request, $data);
        }

        if ($request->getMethod() === 'PUT') { // Create a Database
            return $this->createDatabase($request, $data);
        }

        return new HandlerResult(400, sprintf('Invalid HTTP method %s', $request->getMethod()));
    }

    /**
     * Creates and returns a new Document instance
     *
     * @param RequestInterface $request
     * @param mixed            $data
     * @return HandlerResultInterface
     */
    protected function createDataInstance(RequestInterface $request, $data): HandlerResultInterface
    {
        $database = $this->getDatabaseForRequest($request);
        if (!$database) {
            return new NotFoundResult(
                sprintf(
                    'Database with identifier "%s" not found',
                    $request->getDatabaseIdentifier()
                ),
                1502447992
            );
        }

        $document = new Document($data);

        if ($request->getDataIdentifier()) {
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
        $this->eventEmitter->scheduleFutureEmit(Event::DOCUMENT_CREATED, [$document]);

        return new HandlerResult(
            201,
            $document
        );
    }

    /**
     * Returns the database for the given request or NULL if it is not specified
     *
     * @param RequestInterface $request
     * @return DatabaseInterface|NULL
     */
    public function getDatabaseForRequest(RequestInterface $request)
    {
        if (!$request->getDatabaseIdentifier()) {
            return null;
        }
        $databaseIdentifier = $request->getDatabaseIdentifier();
//		if (!$this->coordinator->databaseExists($databaseIdentifier)) {
//			return NULL;
//		}
        try {
            return $this->coordinator->getDatabase($databaseIdentifier);
        } catch (ReaderException $exception) {
            return null;
        }
    }

    /**
     * Creates and returns a new Database
     *
     * @param RequestInterface $request
     * @param mixed            $data
     * @return HandlerResult
     */
    protected function createDatabase(RequestInterface $request, $data)
    {
        if ($request->getDataIdentifier()) {
            throw new InvalidRequestParameterException(
                'Document identifier in request path is not allowed when creating a Database',
                1413278767
            );
        }

        if ($data) {
            DebugUtility::pl('Database creation parameters are currently not supported');
        }
        $databaseIdentifier = $request->getDatabaseIdentifier();
        $database = $this->coordinator->createDatabase($databaseIdentifier);
        if ($database) {
            $this->eventEmitter->scheduleFutureEmit(Event::DATABASE_CREATED, [$database]);

            return new HandlerResult(201, sprintf('Database "%s" created', $databaseIdentifier));
        } else {
            return new HandlerResult(400);
        }
    }

    public function read(RequestInterface $request): HandlerResultInterface
    {
        $query = $request->getQuery();

        // Extract the Expand configuration from the query
        $expandConfiguration = null;
        if (isset($query[Constants::EXPAND_KEYWORD]) && $query[Constants::EXPAND_KEYWORD]) {
            $expandConfiguration = $this->expandConfigurationBuilder->buildExpandConfigurations(
                $query[Constants::EXPAND_KEYWORD]
            );
            unset($query[Constants::EXPAND_KEYWORD]);
        }

        // If a Data identifier load and return the Document instance
        if ($request->getDataIdentifier()) {
            $document = $this->getDataForRequest($request);
            if ($document) {
                if ($expandConfiguration) {
                    $collection = $this->expandObjectsInCollection([$document], $expandConfiguration);
                    $document = $collection[0];
                }

                return new HandlerResult(
                    200,
                    $document
                );
            } else {
                return new NotFoundResult(
                    sprintf(
                        'Document instance with identifier "%s" not found in database "%s"',
                        $request->getDataIdentifier(),
                        $request->getDatabaseIdentifier()
                    ),
                    1502448024
                );
            }
        }

        $database = $this->getDatabaseForRequest($request);
        if (!$database) {
            return new NotFoundResult(
                sprintf(
                    'Database with identifier "%s" not found',
                    $request->getDatabaseIdentifier()
                ),
                1502448032
            );
        }


        // If there is a filter defined
        if ($query) {
            $filter = $this->filterBuilder->buildFilter($query);
            $filterResult = $filter->filterCollection($database);
            $filterResultCount = $filterResult->count();
            $statusCode = $filterResultCount > 0 ? 200 : 404;
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
     * @param RequestInterface $request
     * @return DocumentInterface|NULL
     */
    public function getDataForRequest(RequestInterface $request)
    {
        if (!$request->getDataIdentifier()) {
            return null;
        }
        $database = $this->getDatabaseForRequest($request);

        return $database ? $database->findByIdentifier($request->getDataIdentifier()) : null;
    }

    public function update(RequestInterface $request, $data): HandlerResultInterface
    {
        if (!$request->getDataIdentifier()) {
            throw new InvalidRequestParameterException('Document identifier is missing', 1413292389);
        }
        $document = $this->getDataForRequest($request);
        if (!$document) {
            return new NotFoundResult(
                sprintf(
                    'Document instance with identifier "%s" not found in database "%s"',
                    $request->getDataIdentifier(),
                    $request->getDatabaseIdentifier()
                ),
                1502447965
            );
        }

        $database = $this->getDatabaseForRequest($request);

        $data[Constants::DATA_ID_KEY] = $request->getDataIdentifier();
        $newDocument = new Document($data, $database->getIdentifier());
        $database->update($newDocument);
        $this->eventEmitter->scheduleFutureEmit(Event::DOCUMENT_UPDATED, [$document]);

        return new HandlerResult(200, $newDocument);
    }

    public function delete(RequestInterface $request): HandlerResultInterface
    {
        $database = $this->getDatabaseForRequest($request);
        if (!$database) {
            return new NotFoundResult(
                sprintf(
                    'Database with identifier "%s" not found',
                    $request->getDatabaseIdentifier()
                ),
                1413035859
            );
        }

        if (!$request->getDataIdentifier()) {
            $databaseIdentifier = $database->getIdentifier();
            $this->coordinator->dropDatabase($databaseIdentifier);
            $this->eventEmitter->scheduleFutureEmit(Event::DATABASE_DELETED, [$database]);

            return new HandlerResult(
                self::HTTP_STATUS_CODE_DELETED,
                sprintf('Database "%s" deleted', $databaseIdentifier)
            );
        }

        $document = $this->getDataForRequest($request);
        if (!$document) {
            return new NotFoundResult(
                sprintf(
                    'Document with identifier "%s" not found in database "%s"',
                    $request->getDataIdentifier(),
                    $request->getDatabaseIdentifier()
                ),
                1413035855
            );
        }
        $database->remove($document);

        $this->eventEmitter->scheduleFutureEmit(Event::DOCUMENT_DELETED, [$document]);

        return new HandlerResult(
            self::HTTP_STATUS_CODE_DELETED,
            sprintf('Document "%s" deleted', $request->getDataIdentifier())
        );
    }

    public function getStatsAction(RequestInterface $request): HandlerResultInterface
    {
        return new HandlerResult(200, $this->server->collectStatistics());
    }

    public function getAssetAction(RequestInterface $request): HandlerResultInterface
    {
        $uri = $request->getPath();
        $uri = substr($uri, 8); // Remove "/_asset/"
        if ($this->assetLoader->hasAssetForUri($uri)) {
            $noCache = false;
            if (is_array($request->getQuery()) && isset($request->getQuery()['noCache'])) {
                $noCache = true;
            }
            /** @var AssetInterface $asset */
            $asset = $this->assetLoader->getAssetForUri($uri, $noCache);

            return new RawResult(200, (string)$asset->getContent(), (string )$asset->getContentType());
        }

        return new NotFoundResult(sprintf('No resource found for "%s"', $uri), 1502448059);
    }

    public function getAllDbsAction(RequestInterface $request): HandlerResultInterface
    {
        return new HandlerResult(200, $this->coordinator->listDatabases());
    }

    public function getCountAction(RequestInterface $request): HandlerResultInterface
    {
        $count = null;
        $readResult = $this->read($request);
        $data = $readResult->getData();
        if ($data instanceof DatabaseInterface || $data instanceof FilterResultInterface) {
            $count = $data->count();
        }
        if ($count === null) {
            return new HandlerResult(
                400, sprintf(
                    'Could not count result of type %s',
                    (is_object($data) ? get_class($data) : gettype($data))
                )
            );
        }

        return new HandlerResult(200, ['count' => $count]);
    }

    /**
     * Expand the objects in the given collection according to the given Expand configurations
     *
     * @param SplFixedArray|DocumentInterface[] $collection
     * @param ExpandConfigurationInterface[]    $expandConfigurationCollection
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

            return SplFixedArray::fromArray([$item]);
        }

        $clonedObjectCollection = new SplFixedArray($collectionCount);
        $i = 0;
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
