<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server\Dispatcher;

use Cundd\Stairtower\Server\Controller\ControllerInterface;
use Cundd\Stairtower\Server\Controller\ControllerResultInterface;
use Cundd\Stairtower\Server\ValueObject\RequestInterface;


/**
 * Interface for classes that can dispatch controller actions that will be handled by a custom Controller
 */
interface ControllerActionDispatcherInterface
{
    /**
     * Dispatches the given Controller/Action request action
     *
     * @param RequestInterface $request
     * @return ControllerResultInterface Returns the Handler Result if the request is not delayed
     * @internal param ResponseInterface $response
     */
    public function dispatchControllerAction(
        RequestInterface $request
    ): ControllerResultInterface;

    /**
     * Handles the given Controller/Action request action
     *
     * @param RequestInterface    $request
     * @param ControllerInterface $controller
     * @return ControllerResultInterface Returns the Handler Result
     * @internal param ResponseInterface $response
     */
    public function invokeControllerActionWithRequest(
        RequestInterface $request,
        ControllerInterface $controller
    ): ControllerResultInterface;

    /**
     * Returns the Controller instance for the given request or NULL if none will be used
     *
     * @param RequestInterface $request
     * @return ControllerInterface|null
     */
    public function getControllerForRequest(RequestInterface $request): ?ControllerInterface;
}
