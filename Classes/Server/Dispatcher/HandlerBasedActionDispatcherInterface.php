<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 08.01.15
 * Time: 11:35
 */

namespace Cundd\PersistentObjectStore\Server\Dispatcher;

use Cundd\PersistentObjectStore\Server\Handler\HandlerInterface;
use React\Http\Request;

/**
 * Interface for classes that can determine a Handler implementation for the given Request
 *
 * @package Cundd\PersistentObjectStore\Server\Dispatcher
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