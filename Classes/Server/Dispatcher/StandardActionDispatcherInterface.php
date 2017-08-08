<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server\Dispatcher;

use Cundd\Stairtower\Server\Handler\HandlerResultInterface;
use Cundd\Stairtower\Server\ValueObject\RequestInterface;
use React\Stream\WritableStreamInterface;

/**
 * Interface for classes that can dispatch standard actions that will be handled by a Handler implementation
 */
interface StandardActionDispatcherInterface extends HandlerBasedActionDispatcherInterface
{
    /**
     * Dispatches the standard action
     *
     * @param RequestInterface        $request
     * @param WritableStreamInterface $response
     * @return HandlerResultInterface Returns the Handler Result if the request is not delayed
     */
    public function dispatchStandardAction(RequestInterface $request, WritableStreamInterface $response);
}