<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Server\Dispatcher;


use Cundd\PersistentObjectStore\Server\Controller\ControllerInterface;
use Cundd\PersistentObjectStore\Server\Controller\ControllerResultInterface;
use Cundd\PersistentObjectStore\Server\ValueObject\Request;
use React\Http\Response;

/**
 * Interface for classes that can dispatch controller actions that will be handled by a custom Controller
 */
interface ControllerActionDispatcherInterface
{
    /**
     * Dispatches the given Controller/Action request action
     *
     * @param Request              $request
     * @param \React\Http\Response $response
     * @return ControllerResultInterface Returns the Handler Result if the request is not delayed
     */
    public function dispatchControllerAction($request, $response);

    /**
     * Handles the given Controller/Action request action
     *
     * @param Request             $request
     * @param Response            $response
     * @param ControllerInterface $controller
     * @return ControllerResultInterface Returns the Handler Result
     */
    public function invokeControllerActionWithRequest($request, $response, $controller);

    /**
     * Returns the Controller instance for the given request or false if none will be used
     *
     * @param Request $request
     * @return ControllerInterface
     */
    public function getControllerForRequest($request);
}