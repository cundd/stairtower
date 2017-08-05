<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Server\Dispatcher;


use Cundd\PersistentObjectStore\Server\Controller\ControllerResultInterface;

/**
 * Interface for classes that can dispatch special Handler actions
 */
interface SpecialHandlerActionDispatcherInterface extends HandlerBasedActionDispatcherInterface
{
    /**
     * Dispatches the given Controller/Action request action
     *
     * @param \React\Http\Request  $request
     * @param \React\Http\Response $response
     * @return ControllerResultInterface Returns the Handler Result if the request is not delayed
     */
    public function dispatchSpecialHandlerAction($request, $response);
}