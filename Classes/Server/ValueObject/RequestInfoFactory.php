<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 10.10.14
 * Time: 14:59
 */

namespace Cundd\PersistentObjectStore\Server\ValueObject;

use Cundd\PersistentObjectStore\Server\Cookie\CookieParserInterface;
use Cundd\PersistentObjectStore\Server\Exception\InvalidRequestActionException;
use Cundd\PersistentObjectStore\Utility\GeneralUtility;
use React\Http\Request as BaseRequest;

/**
 * Factory for Request instances
 *
 * @package Cundd\PersistentObjectStore\Server\ValueObject
 */
class RequestInfoFactory
{
    const DEFAULT_ACTION = 'index';

    /**
     * @var CookieParserInterface
     * @Inject
     */
    protected $cookieParser;

    /**
     * Builds a Request instance for the given raw request
     *
     * @param BaseRequest|Request $request
     * @param bool                $parseCookies Define if the cookies should be parsed
     * @return Request
     */
    public function buildRequestFromRawRequest($request, $parseCookies = false)
    {
        if ($request instanceof Request) {
            return $request;
        }

        $pathParts           = explode('/', $request->getPath());
        $pathParts           = array_values(array_filter($pathParts));
        $dataIdentifier      = null;
        $databaseIdentifier  = null;
        $controllerClassName = null;

        if (count($pathParts) >= 2) {
            $dataIdentifier = $pathParts[1];
        }
        if (count($pathParts) >= 1) {
            $databaseIdentifier = $pathParts[0];
        }
        $handlerAction = static::getHandlerActionForRequest($request);
        if ($databaseIdentifier && $databaseIdentifier[0] === '_') {
            $databaseIdentifier = '';
        }
        if ($dataIdentifier && $dataIdentifier[0] === '_') {
            $dataIdentifier = '';
        }

        $controllerAndActionArray = $this->getControllerAndActionForRequest($request);
        if ($controllerAndActionArray) {
            list($controllerClassName, $handlerAction) = $controllerAndActionArray;

            $databaseIdentifier = isset($pathParts[2]) ? $pathParts[2] : null;
            $dataIdentifier     = isset($pathParts[3]) ? $pathParts[3] : null;
        }

        $cookies = [];
        if ($parseCookies) {
            $cookies = $this->cookieParser->parse($request);
        }

        return new Request(
            $request,
            $dataIdentifier,
            $databaseIdentifier,
            $request->getMethod(),
            ($handlerAction !== false ? $handlerAction : null),
            $controllerClassName,
            null,
            $cookies
        );
    }

    /**
     * Builds a Request instance for the given request
     *
     * @param BaseRequest|Request $request
     * @return Request
     * @deprecated
     */
    public static function buildRequestInfoFromRequest($request)
    {
        static $factoryInstance = null;
        if (!$factoryInstance) {
            $factoryInstance = new self();
        }
        return $factoryInstance->buildRequestFromRawRequest($request);
    }

    /**
     * Returns the handler class if the path contains a special information identifier, otherwise the Handler interface
     * name
     *
     * @param Request|BaseRequest $request
     * @return string
     */
    public static function getHandlerClassForRequest($request)
    {
        $default = 'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface';
        $path    = $request->getPath();
        if (!$path) {
            return $default;
        }
        $path = ltrim($path, '/');
        if (!$path || $path[0] !== '_') {
            return $default;
        }

        $handlerIdentifier = strstr($path, '/', true);
        if ($handlerIdentifier === false) {
            $handlerIdentifier = $path;
        }
        $handlerIdentifier = substr($handlerIdentifier, 1);

        // Generate the Application name
        $applicationName = str_replace(' ', '\\', ucwords(str_replace('_', ' ', $handlerIdentifier))) . '\\Application';
        if (class_exists($applicationName)) {
            return $applicationName;
        }

        // Generate the Handler name
        $handlerName = sprintf(
            'Cundd\\PersistentObjectStore\\Server\\Handler\\%sHandler',
            ucfirst($handlerIdentifier)
        );
        if (class_exists($handlerName)) {
            return $handlerName;
        }

        return $default;
    }

    /**
     * Get the controller class and action name
     *
     * Returns the controller class and action name as array if the path contains a special information identifier. If
     * no special information identifier is given, or the controller class does not exist false is returned.
     *
     * @param Request $request
     * @return array|boolean
     */
    public function getControllerAndActionForRequest($request)
    {
        $path = $request->getPath();
        if (!$path) {
            return false;
        }
        $path = ltrim($path, '/');
        if (!$path || $path[0] !== '_') {
            return false;
        }
        if (strpos($path, '-') === false) {
            return false;
        }

        $pathParts = explode('/', substr($path, 1));
        if (count($pathParts) < 2) {
            return false;
        }
        list($controllerIdentifier, $actionIdentifier) = $pathParts;

        // If no action identifier is found use the index action
        if (!trim($actionIdentifier)) {
            $actionIdentifier = self::DEFAULT_ACTION;
        }

        // Generate the Controller class name
        $controllerClassName = $controllerIdentifier;
        $controllerClassName = str_replace(' ', '', ucwords(str_replace('_', ' ', $controllerClassName)));
        $lastUnderscore      = strrpos($controllerClassName, '-');
        $controllerClassName = str_replace(' ', '\\', ucwords(str_replace('-', ' ', $controllerClassName)));
        $controllerClassName = ''
            . substr($controllerClassName, 0, $lastUnderscore + 1)
            . 'Controller\\'
            . ucfirst(substr($controllerClassName, $lastUnderscore + 1))
            . 'Controller';

        if (!class_exists($controllerClassName)) {
            return false;
        }

        $method     = $request->getMethod();
        $actionName = GeneralUtility::underscoreToCamelCase(strtolower($method) . '_' . $actionIdentifier) . 'Action';
        if (!ctype_alnum($actionName)) {
            throw new InvalidRequestActionException('Action name must be alphanumeric', 1420547305);
        }

        // Don't check if the action exists here
        // if (!method_exists($controllerClassName, $actionName)) return false;

        return array($controllerClassName, $actionName);
    }

    /**
     * Returns the handler action if the path contains a special information identifier, otherwise FALSE
     *
     * @param Request|BaseRequest $request
     * @return string|bool
     */
    public static function getHandlerActionForRequest($request)
    {
        return static::getActionForRequestAndClass(
            $request,
            self::getHandlerClassForRequest($request)
        );

    }

    /**
     * Returns an action method name if the path contains a special information identifier, otherwise FALSE
     *
     * @param Request $request
     * @param string  $interface
     * @return string|bool
     */
    protected static function getActionForRequestAndClass($request, $interface)
    {
        $path   = $request->getPath();
        $method = $request->getMethod();
        if (!$path) {
            return false;
        }
        if ($path[0] === '/') {
            $path = substr($path, 1);
        }
        $pathParts = explode('/', $path);

        foreach ($pathParts as $currentPathPart) {
            if ($currentPathPart && $currentPathPart[0] === '_') {
                $handlerAction = GeneralUtility::underscoreToCamelCase(
                        strtolower($method) . '_' . substr($currentPathPart, 1)
                    ) . 'Action';
                if (method_exists($interface, $handlerAction)) {
                    return $handlerAction;
                }
            }
        }

        return false;
    }

    /**
     * Returns the special server action if the path contains a special information identifier, otherwise FALSE
     *
     * @param Request|BaseRequest $request
     * @return string|bool
     */
    public static function getServerActionForRequest($request)
    {
        $path   = $request->getPath();
        $method = $request->getMethod();
        $path = ltrim($path, '/');
        if (!$path || $path[0] !== '_' || $method !== 'POST') {
            return false;
        }
        list($action,) = explode('/', substr($path, 1), 2);

        if (in_array($action, array('shutdown', 'restart',))) {
            return $action;
        }

        return false;
    }

    /**
     * Creates a copy of the given Request Info with the given body
     *
     * @param Request $requestInfo
     * @param mixed       $body
     * @return Request
     */
    public static function copyWithBody($requestInfo, $body)
    {
        return new Request(
            $requestInfo->getOriginalRequest(),
            $requestInfo->getDataIdentifier(),
            $requestInfo->getDatabaseIdentifier(),
            $requestInfo->getMethod(),
            $requestInfo->getAction(),
            $requestInfo->getControllerClass(),
            $body,
            $requestInfo->getCookies()
        );
    }
}