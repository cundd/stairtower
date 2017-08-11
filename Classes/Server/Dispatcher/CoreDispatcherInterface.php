<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server\Dispatcher;

use Psr\Http\Message\ServerRequestInterface;
use React\Promise\Promise;


/**
 * Core class to dispatch a Request to the different handlers
 *
 * The dispatcher is also responsible to collect the full Request body if necessary and forward an enriched Request
 * instance to the handlers
 */
interface CoreDispatcherInterface
{
    /**
     * Dispatch the Request
     *
     * The method returns a Promise and will start the dispatching to the different handlers
     *
     * @param ServerRequestInterface $serverRequest
     * @return Promise
     */
    public function dispatch(ServerRequestInterface $serverRequest);
}
