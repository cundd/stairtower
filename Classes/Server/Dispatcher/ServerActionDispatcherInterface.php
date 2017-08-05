<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Server\Dispatcher;

/**
 * Interface for classes that can dispatch server actions
 */
interface ServerActionDispatcherInterface
{
    /**
     * Dispatch the given server action
     *
     * @param string               $serverAction
     * @param \React\Http\Request  $request
     * @param \React\Http\Response $response
     */
    public function dispatchServerAction($serverAction, $request, $response);
}