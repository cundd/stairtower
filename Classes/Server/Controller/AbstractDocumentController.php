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
use Cundd\PersistentObjectStore\Domain\Model\Document;
use Cundd\PersistentObjectStore\Domain\Model\DocumentInterface;
use Cundd\PersistentObjectStore\Server\ServerInterface;
use Cundd\PersistentObjectStore\Server\ValueObject\RequestInfo;
use ReflectionClass;

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
            return $coordinator->getDatabase($databaseIdentifier);
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

    /**
     * Returns the argument to be passed to the action
     *
     * @param RequestInfo $requestInfo Request info object
     * @param string $action Action name
     * @param bool $noArgument Reference the will be set to true if no argument should be passed
     * @return Document|null
     */
    protected function prepareArgumentForRequestAndAction($requestInfo, $action, &$noArgument = false)
    {
        $requiresDocumentArgument = $this->checkIfActionRequiresDocumentArgument($action);
        if ($requiresDocumentArgument === 0) {
            $noArgument = true;
            return null;
        }

        $requestBody = $requestInfo->getBody();
        if ($requiresDocumentArgument > 0) {
            if ($requestBody !== null) {
                return new Document($requestBody);
            } else {
                return $this->getDocumentForCurrentRequest();
            }
        } elseif ($requestBody !== null) {
            return $requestBody;
        }
        return null;
    }

    /**
     * Returns if the controller action requires a Document as parameter
     *
     * TODO: Move this functionality into a separate class
     * @param string $actionMethod Method name
     * @return int Returns 1 if a Document is required, 2 if it is optional otherwise 0
     */
    protected function checkIfActionRequiresDocumentArgument($actionMethod)
    {
        static $controllerActionRequiresDocumentCache = array();
        $controllerClass = get_class($this);
        $controllerActionIdentifier = $controllerClass . '::' . $actionMethod;

        if (isset($controllerActionRequiresDocumentCache[$controllerActionIdentifier])) {
            return $controllerActionRequiresDocumentCache[$controllerActionIdentifier];
        }

        $classReflection = new ReflectionClass($controllerClass);
        $methodReflection = $classReflection->getMethod($actionMethod);
        $controllerActionRequiresDocumentCache[$controllerActionIdentifier] = 0;
        foreach ($methodReflection->getParameters() as $parameter) {
            $argumentClassName = $parameter->getClass() ? trim($parameter->getClass()->getName()) : null;
            if ($argumentClassName && $argumentClassName === 'Cundd\\PersistentObjectStore\\Domain\\Model\\Document') {
                $controllerActionRequiresDocumentCache[$controllerActionIdentifier] = ($parameter->isOptional() ? 2 : 1);
                break;
            }
        }
        return $controllerActionRequiresDocumentCache[$controllerActionIdentifier];
    }
}