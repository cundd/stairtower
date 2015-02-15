<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 12.02.15
 * Time: 21:04
 */

namespace Cundd\PersistentObjectStore\Server\Controller;

use Cundd\PersistentObjectStore\Configuration\ConfigurationManager;
use Cundd\PersistentObjectStore\DataAccess\Exception\ReaderException;
use Cundd\PersistentObjectStore\Domain\Model\DatabaseInterface;
use Cundd\PersistentObjectStore\Domain\Model\DocumentInterface;
use Cundd\PersistentObjectStore\Server\ServerInterface;
use Cundd\PersistentObjectStore\Server\ValueObject\RequestInfo;

/**
 * An abstract Document based Controller
 *
 * @package Cundd\Sa\Controller
 */
abstract class AbstractDocumentController extends AbstractController implements DocumentControllerInterface
{
    /**
     * Document Access Coordinator
     *
     * @var \Cundd\PersistentObjectStore\DataAccess\CoordinatorInterface
     * @Inject
     */
    protected $coordinator;

    /**
     * Returns the base path
     *
     * @return string
     */
    public function getBasePath()
    {
        return ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('basePath');
    }

    /**
     * Returns if the server is in development mode
     *
     * @return bool
     */
    public function isDevelopmentMode()
    {
        return ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('serverMode') === ServerInterface::SERVER_MODE_DEVELOPMENT;
    }

    /**
     * Returns the coordinator
     *
     * @return \Cundd\PersistentObjectStore\DataAccess\CoordinatorInterface
     */
    public function getCoordinator()
    {
        return $this->coordinator;
    }

    /**
     * Returns the database for the current Request Info
     *
     * @return DatabaseInterface|null
     */
    public function getDatabaseForCurrentRequest()
    {
        return $this->getDatabaseForRequestInfo($this->getRequestInfo());
    }

    /**
     * Returns the database for the given request or null if it is not specified
     *
     * @param RequestInfo $requestInfo
     * @return DatabaseInterface|null
     */
    public function getDatabaseForRequestInfo(RequestInfo $requestInfo)
    {
        if (!$requestInfo->getDatabaseIdentifier()) {
            return null;
        }
        $coordinator        = $this->getCoordinator();
        $databaseIdentifier = $requestInfo->getDatabaseIdentifier();
        if (!$coordinator->databaseExists($databaseIdentifier)) {
            return null;
        }
        try {
            return clone $coordinator->getDatabase($databaseIdentifier);
        } catch (ReaderException $exception) {
            return null;
        }
    }

    /**
     * Returns the Document for the current Request Info
     *
     * @return DocumentInterface|null
     */
    public function getDocumentForCurrentRequest()
    {
        return $this->getDocumentForRequest($this->getRequestInfo());
    }

    /**
     * Returns the Document for the given request or null if it is not specified
     *
     * @param RequestInfo $requestInfo
     * @return DocumentInterface|null
     */
    public function getDocumentForRequest(RequestInfo $requestInfo)
    {
        if (!$requestInfo->getDataIdentifier()) {
            return null;
        }
        $database = $this->getDatabaseForRequestInfo($requestInfo);
        if (!$database) {
            return null;
        }

        $document = $database->findByIdentifier($requestInfo->getDataIdentifier());
        if ($document) {
            return clone $document;
        }
        return null;
    }
}