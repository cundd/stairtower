<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Server\Dispatcher;

use Cundd\PersistentObjectStore\Server\ValueObject\RequestInterface;
use React\Stream\WritableStreamInterface;

/**
 * Interface for classes that can dispatch server actions
 */
interface ServerActionDispatcherInterface
{
    /**
     * Dispatch the given server action
     *
     * @param string                  $serverAction
     * @param RequestInterface        $request
     * @param WritableStreamInterface $response
     * @return
     */
    public function dispatchServerAction(
        string $serverAction,
        RequestInterface $request,
        WritableStreamInterface $response
    );
}