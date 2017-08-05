<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Server\Dispatcher;

use Cundd\PersistentObjectStore\Server\Handler\HandlerInterface;
use Cundd\PersistentObjectStore\Server\ValueObject\Request;

/**
 * Interface for classes that can determine a Handler implementation for the given Request
 */
interface HandlerBasedActionDispatcherInterface
{
    /**
     * Returns the handler for the given request
     *
     * @param Request $request
     * @return HandlerInterface
     */
    public function getHandlerForRequest(Request $request);
}