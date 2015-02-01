<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 10.10.14
 * Time: 14:59
 */

namespace Cundd\PersistentObjectStore\Server\ValueObject;

use Cundd\PersistentObjectStore\Utility\ClassLoaderUtility;
use Cundd\PersistentObjectStore\Utility\GeneralUtility;
use React\Http\Request;

/**
 * Factory for RequestInfo instances
 *
 * @package Cundd\PersistentObjectStore\Server\ValueObject
 */
class RequestInfoFactory
{
    /**
     * Map of paths to their RequestInfo objects
     *
     * @var array
     */
    protected static $pathToRequestInfoMap = array();

    /**
     * Builds a RequestInfo instance for the given path
     *
     * @param Request $request
     * @return RequestInfo
     */
    public static function buildRequestInfoFromRequest(Request $request)
    {
        $requestInfoIdentifier = sha1(sprintf('%s-%s-%s', $request->getMethod(), $request->getPath(),
            json_encode($request->getQuery())));
        if (!isset(static::$pathToRequestInfoMap[$requestInfoIdentifier])) {
            $pathParts          = explode('/', $request->getPath());
            $pathParts          = array_values(array_filter($pathParts, function ($item) {
                return !!$item;
            }));
            $dataIdentifier     = null;
            $databaseIdentifier = null;

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
            static::$pathToRequestInfoMap[$requestInfoIdentifier] = new RequestInfo($request, $dataIdentifier,
                $databaseIdentifier, $request->getMethod(), $handlerAction);
        }
        return static::$pathToRequestInfoMap[$requestInfoIdentifier];
    }

    /**
     * Returns the handler class if the path contains a special information identifier, otherwise the Handler interface
     * name
     *
     * @param Request $request
     * @return string
     */
    public static function getHandlerClassForRequest($request)
    {
        $default = 'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface';
        $path    = $request->getPath();
        if (!$path) {
            return $default;
        }
        if ($path[0] === '/') {
            $path = substr($path, 1);
        }
        if ($path[0] !== '_') {
            return $default;
        }

        $handlerIdentifier = strstr($path, '/', true);
        if ($handlerIdentifier === false) {
            $handlerIdentifier = $path;
        }
        $handlerIdentifier = substr($handlerIdentifier, 1);

        // Generate the Application name
        $applicationName = str_replace(' ', '\\', ucwords(str_replace('_', ' ', $handlerIdentifier))) . '\\Application';

        if (ClassLoaderUtility::classExists($applicationName)) {
            return $applicationName;
        }

        // Generate the Handler name
        $handlerName = sprintf(
            'Cundd\\PersistentObjectStore\\Server\\Handler\\%sHandler',
            ucfirst($handlerIdentifier)
        );
        if (ClassLoaderUtility::classExists($handlerName)) {
            return $handlerName;
        }
        return $default;
    }

    /**
     * Returns the handler action if the path contains a special information identifier, otherwise FALSE
     *
     * @param Request $request
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
     * @param Request $request
     * @return string|bool
     */
    public static function getServerActionForRequest($request)
    {
        $path   = $request->getPath();
        $method = $request->getMethod();
        if ($path[0] === '/') {
            $path = substr($path, 1);
        }
        if ($path[0] !== '_' || $method !== 'POST') {
            return false;
        }
        list($action,) = explode('/', substr($path, 1), 2);

        if (in_array($action, array('shutdown', 'restart',))) {
            return $action;
        }
        return false;
    }
} 