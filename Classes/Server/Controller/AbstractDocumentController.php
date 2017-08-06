<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Server\Controller;

use Cundd\PersistentObjectStore\Configuration\ConfigurationManager;
use Cundd\PersistentObjectStore\DataAccess\CoordinatorInterface;
use Cundd\PersistentObjectStore\DataAccess\Exception\ReaderException;
use Cundd\PersistentObjectStore\Domain\Model\DatabaseInterface;
use Cundd\PersistentObjectStore\Domain\Model\Document;
use Cundd\PersistentObjectStore\Domain\Model\DocumentInterface;
use Cundd\PersistentObjectStore\Server\ServerInterface;
use Cundd\PersistentObjectStore\Server\ValueObject\RequestInterface;
use ReflectionClass;

/**
 * An abstract Document based Controller
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
        return ConfigurationManager::getSharedInstance()
                ->getConfigurationForKeyPath('serverMode') === ServerInterface::SERVER_MODE_DEVELOPMENT;
    }

    /**
     * Returns the coordinator
     *
     * @return \Cundd\PersistentObjectStore\DataAccess\CoordinatorInterface
     */
    public function getCoordinator(): CoordinatorInterface
    {
        return $this->coordinator;
    }

    /**
     * Returns the database for the current Request
     *
     * @return DatabaseInterface|null
     */
    public function getDatabaseForCurrentRequest(): ?DatabaseInterface
    {
        return $this->getDatabaseForRequest($this->getRequest());
    }

    /**
     * Returns the database for the given request or null if it is not specified
     *
     * @param RequestInterface $request
     * @return DatabaseInterface|null
     */
    public function getDatabaseForRequest(RequestInterface $request): ?DatabaseInterface
    {
        if (!$request->getDatabaseIdentifier()) {
            return null;
        }
        $coordinator = $this->getCoordinator();
        $databaseIdentifier = $request->getDatabaseIdentifier();
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
    public function getDocumentForCurrentRequest(): ?DocumentInterface
    {
        return $this->getDocumentForRequest($this->getRequest());
    }

    /**
     * Returns the Document for the given request or null if it is not specified
     *
     * @param RequestInterface $request
     * @return DocumentInterface|null
     */
    public function getDocumentForRequest(RequestInterface $request): ?DocumentInterface
    {
        if (!$request->getDataIdentifier()) {
            return null;
        }
        $database = $this->getDatabaseForRequest($request);
        if (!$database) {
            return null;
        }

        $document = $database->findByIdentifier($request->getDataIdentifier());
        if ($document) {
            return clone $document;
        }

        return null;
    }

    /**
     * Returns the argument to be passed to the action
     *
     * @param RequestInterface $request    Request info object
     * @param string           $action     Action name
     * @param bool             $noArgument Reference the will be set to true if no argument should be passed
     * @return DocumentInterface|null
     */
    protected function prepareArgumentForRequestAndAction(
        RequestInterface $request,
        string $action,
        &$noArgument = false
    ) {
        $requiresDocumentArgument = $this->checkIfActionRequiresDocumentArgument($action);
        if ($requiresDocumentArgument === 0) {
            $noArgument = true;

            return null;
        }

        $requestBody = $request->getBody();
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
     *
     * @param string $actionMethod Method name
     * @return int Returns 1 if a Document is required, 2 if it is optional otherwise 0
     */
    protected function checkIfActionRequiresDocumentArgument(string $actionMethod)
    {
        static $controllerActionRequiresDocumentCache = [];
        $controllerClass = get_class($this);
        $controllerActionIdentifier = $controllerClass . '::' . $actionMethod;

        if (isset($controllerActionRequiresDocumentCache[$controllerActionIdentifier])) {
            return $controllerActionRequiresDocumentCache[$controllerActionIdentifier];
        }

        $classReflection = new ReflectionClass($controllerClass);
        $methodReflection = $classReflection->getMethod(
            $actionMethod
        );
        $controllerActionRequiresDocumentCache[$controllerActionIdentifier] = 0;
        foreach ($methodReflection->getParameters() as $parameter) {
            $argumentClassName = $parameter->getClass() ? trim($parameter->getClass()->getName()) : null;
            if (!$argumentClassName) {
                continue;
            }
            if (
                $argumentClassName === Document::class
                || $argumentClassName === DocumentInterface::class
                || in_array(
                    DocumentInterface::class,
                    (array)class_implements($argumentClassName, true)
                )
            ) {
                $controllerActionRequiresDocumentCache[$controllerActionIdentifier] = $parameter->isOptional() ? 2 : 1;
                break;
            }
        }

        return $controllerActionRequiresDocumentCache[$controllerActionIdentifier];
    }
}
