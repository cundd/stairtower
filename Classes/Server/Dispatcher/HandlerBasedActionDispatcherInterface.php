<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server\Dispatcher;

use Cundd\Stairtower\Server\Handler\HandlerInterface;
use Cundd\Stairtower\Server\ValueObject\RequestInterface;

/**
 * Interface for classes that can determine a Handler implementation for the given Request
 */
interface HandlerBasedActionDispatcherInterface
{
    /**
     * Returns the handler for the given request
     *
     * @param RequestInterface $request
     * @return HandlerInterface
     */
    public function getHandlerForRequest(RequestInterface $request);
}
