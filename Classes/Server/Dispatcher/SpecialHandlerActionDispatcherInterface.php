<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 08.01.15
 * Time: 11:35
 */

namespace Cundd\PersistentObjectStore\Server\Dispatcher;


use Cundd\PersistentObjectStore\Server\Controller\ControllerResultInterface;

/**
 * Interface for classes that can dispatch special Handler actions
 *
 * @package Cundd\PersistentObjectStore\Server\Dispatcher
 */
interface SpecialHandlerActionDispatcherInterface extends HandlerBasedActionDispatcherInterface
{
    /**
     * Dispatches the given Controller/Action request action
     *
     * @param \React\Http\Request  $request
     * @param \React\Http\Response $response
     * @return ControllerResultInterface Returns the Handler Result if the request is not delayed
     */
    public function dispatchSpecialHandlerAction($request, $response);
}