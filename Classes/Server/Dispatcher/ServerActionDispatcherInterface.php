<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 08.01.15
 * Time: 11:35
 */

namespace Cundd\PersistentObjectStore\Server\Dispatcher;

/**
 * Interface for classes that can dispatch server actions
 *
 * @package Cundd\PersistentObjectStore\Server\Dispatcher
 */
interface ServerActionDispatcherInterface
{
    /**
     * Dispatch the given server action
     *
     * @param string               $serverAction
     * @param \React\Http\Request  $request
     * @param \React\Http\Response $response
     */
    public function dispatchServerAction($serverAction, $request, $response);
}