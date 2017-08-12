<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server\ValueObject;

use Cundd\Stairtower\Server\Exception\InvalidRequestActionException;
use Cundd\Stairtower\Server\Handler\HandlerInterface;
use Cundd\Stairtower\Utility\GeneralUtility;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Factory for Request instances
 */
class RequestInfoFactory
{
    const DEFAULT_ACTION = 'index';

    /**
     * Builds a Request instance for the given raw request
     *
     * @param RequestInterface|ServerRequestInterface $request
     * @return RequestInterface
     */
    public function buildRequestFromRawRequest($request): RequestInterface
    {
        if ($request instanceof RequestInterface) {
            return $request;
        }

        $pathParts = array_values(array_filter(explode('/', $request->getUri()->getPath())));
        $dataIdentifier = '';
        $databaseIdentifier = '';
        $controllerClassName = '';

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

            $databaseIdentifier = $pathParts[2] ?? '';
            $dataIdentifier = $pathParts[3] ?? '';
        }

        return new Request(
            $request,
            $dataIdentifier,
            $databaseIdentifier,
            $request->getMethod(),
            ($handlerAction !== false ? $handlerAction : ''),
            $controllerClassName,
            $request->getBody(),
            $request->getParsedBody()
        );
    }

    /**
     * Returns the handler class if the path contains a special information identifier, otherwise the Handler interface
     * name
     *
     * @param RequestInterface|ServerRequestInterface $request
     * @return string
     */
    public static function getHandlerClassForRequest($request): string
    {
        $path = $request->getUri()->getPath();
        if (!$path) {
            return HandlerInterface::class;
        }
        $path = ltrim($path, '/');
        if (!$path || $path[0] !== '_') {
            return HandlerInterface::class;
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
            'Cundd\\Stairtower\\Server\\Handler\\%sHandler',
            ucfirst($handlerIdentifier)
        );
        if (class_exists($handlerName)) {
            return $handlerName;
        }

        return HandlerInterface::class;
    }

    /**
     * Get the controller class and action name
     *
     * Returns the controller class and action name as array if the path contains a special information identifier. If
     * no special information identifier is given, or the controller class does not exist false is returned.
     *
     * @param RequestInterface|ServerRequestInterface $request
     * @return array|boolean
     */
    public function getControllerAndActionForRequest($request)
    {
        $path = $request->getUri()->getPath();
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
        $lastUnderscore = strrpos($controllerClassName, '-');
        $controllerClassName = str_replace(' ', '\\', ucwords(str_replace('-', ' ', $controllerClassName)));
        $controllerClassName = ''
            . substr($controllerClassName, 0, $lastUnderscore + 1)
            . 'Controller\\'
            . ucfirst(substr($controllerClassName, $lastUnderscore + 1))
            . 'Controller';

        if (!class_exists($controllerClassName)) {
            return false;
        }

        $method = $request->getMethod();
        $actionName = GeneralUtility::underscoreToCamelCase(strtolower($method) . '_' . $actionIdentifier) . 'Action';
        if (!ctype_alnum($actionName)) {
            throw new InvalidRequestActionException('Action name must be alphanumeric', 1420547305);
        }

        // Don't check if the action exists here
        // if (!method_exists($controllerClassName, $actionName)) return false;

        return [$controllerClassName, $actionName];
    }

    /**
     * Returns the handler action if the path contains a special information identifier, otherwise FALSE
     *
     * @param RequestInterface|ServerRequestInterface $request
     * @return string
     */
    public static function getHandlerActionForRequest($request): string
    {
        return static::getActionForRequestAndClass(
            $request,
            self::getHandlerClassForRequest($request)
        );

    }

    /**
     * Returns an action method name if the path contains a special information identifier, otherwise FALSE
     *
     * @param RequestInterface|ServerRequestInterface $request
     * @param string                                  $interface
     * @return string
     */
    protected static function getActionForRequestAndClass($request, $interface): string
    {
        $path = $request->getUri()->getPath();
        $method = $request->getMethod();
        if (!$path) {
            return '';
        }
        if ($path[0] === '/') {
            $path = substr($path, 1);
        }
        $pathParts = explode('/', $path);

        foreach ($pathParts as $currentPathPart) {
            if ($currentPathPart && $currentPathPart[0] === '_') {
                $handlerAction = GeneralUtility::underscoreToCamelCase(
                        strtolower($method) . '_' . substr($currentPathPart, 1)
                    )
                    . 'Action';
                if (method_exists($interface, $handlerAction)) {
                    return $handlerAction;
                }
            }
        }

        return '';
    }

    /**
     * Returns the special server action if the path contains a special information identifier, otherwise FALSE
     *
     * @param RequestInterface|ServerRequestInterface $request
     * @return string
     */
    public static function getServerActionForRequest($request): string
    {
        $path = $request->getUri()->getPath();
        $method = $request->getMethod();
        $path = ltrim($path, '/');
        if (!$path || $path[0] !== '_' || $method !== 'POST') {
            return '';
        }
        list($action,) = explode('/', substr($path, 1), 2);

        if (in_array($action, ['shutdown', 'restart',])) {
            return $action;
        }

        return '';
    }
}
