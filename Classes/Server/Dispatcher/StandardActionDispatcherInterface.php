<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Server\Dispatcher;

use Cundd\PersistentObjectStore\Server\Handler\HandlerResultInterface;

/**
 * Interface for classes that can dispatch standard actions that will be handled by a Handler implementation
 */
interface StandardActionDispatcherInterface extends HandlerBasedActionDispatcherInterface
{
    /**
     * Dispatches the standard action
     *
     * @param \React\Http\Request  $request
     * @param \React\Http\Response $response
     * @return HandlerResultInterface Returns the Handler Result if the request is not delayed
     */
    public function dispatchStandardAction($request, $response);
}