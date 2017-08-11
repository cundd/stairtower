<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server\Dispatcher;

use Cundd\Stairtower\Server\Handler\HandlerResultInterface;
use Cundd\Stairtower\Server\ValueObject\RequestInterface;


/**
 * Interface for classes that can dispatch special Handler actions
 */
interface SpecialHandlerActionDispatcherInterface extends HandlerBasedActionDispatcherInterface
{
    /**
     * Dispatches the given Controller/Action request action
     *
     * @param RequestInterface $request
     * @return HandlerResultInterface Returns the Handler Result if the request is not delayed
     */
    public function dispatchSpecialHandlerAction(RequestInterface $request): HandlerResultInterface;
}
