<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Server\Dispatcher;

use Cundd\PersistentObjectStore\Server\Handler\HandlerResultInterface;
use Cundd\PersistentObjectStore\Server\ValueObject\RequestInterface;
use React\Stream\WritableStreamInterface;

/**
 * Interface for classes that can dispatch special Handler actions
 */
interface SpecialHandlerActionDispatcherInterface extends HandlerBasedActionDispatcherInterface
{
    /**
     * Dispatches the given Controller/Action request action
     *
     * @param RequestInterface        $request
     * @param WritableStreamInterface $response
     * @return HandlerResultInterface Returns the Handler Result if the request is not delayed
     */
    public function dispatchSpecialHandlerAction(
        RequestInterface $request,
        WritableStreamInterface $response
    ): HandlerResultInterface;
}
