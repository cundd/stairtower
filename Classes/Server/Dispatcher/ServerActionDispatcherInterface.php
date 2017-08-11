<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server\Dispatcher;

use Cundd\Stairtower\Server\Handler\HandlerResultInterface;
use Cundd\Stairtower\Server\ValueObject\RequestInterface;


/**
 * Interface for classes that can dispatch server actions
 */
interface ServerActionDispatcherInterface
{
    /**
     * Dispatch the given server action
     *
     * @param string           $serverAction
     * @param RequestInterface $request
     * @return HandlerResultInterface
     */
    public function dispatchServerAction(string $serverAction, RequestInterface $request): HandlerResultInterface;
}
