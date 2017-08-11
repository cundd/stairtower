<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server\Dispatcher;

use Cundd\Stairtower\Server\Handler\HandlerResultInterface;
use Cundd\Stairtower\Server\ValueObject\RequestInterface;


/**
 * Interface for classes that can dispatch standard actions that will be handled by a Handler implementation
 */
interface StandardActionDispatcherInterface extends HandlerBasedActionDispatcherInterface
{
    /**
     * Dispatches the standard action
     *
     * @param RequestInterface $request
     * @return HandlerResultInterface Returns the Handler Result if the request is not delayed
     * @internal param \Psr\Http\Message\ResponseInterface $response
     */
    public function dispatchStandardAction(RequestInterface $request): \Cundd\Stairtower\Server\Handler\HandlerResultInterface;
}