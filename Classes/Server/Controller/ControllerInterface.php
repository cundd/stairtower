<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 08.01.15
 * Time: 10:40
 */

namespace Cundd\PersistentObjectStore\Server\Controller;

use Cundd\PersistentObjectStore\Server\ValueObject\RequestInfo as Request;
use React\Http\Response;

/**
 * Interface for Controllers
 *
 * @package Cundd\PersistentObjectStore\Server\Controller
 */
interface ControllerInterface
{
    /**
     * Initialize the Controller instance
     *
     * This method will be called first in the request handling
     *
     * @return void
     */
    public function initialize();

    /**
     * Sets the Request Info for the current request
     *
     * @param Request $request
     * @return $this
     */
    public function setRequest(Request $request);

    /**
     * Returns the current Request Info instance
     *
     * @return Request
     */
    public function getRequest();

    /**
     * Unset the Request Info instance after the request has been processed
     *
     * This method will be called to free the Request Info instance
     *
     * @return void
     */
    public function unsetRequest();

    /**
     * Will be invoked before the actual action method is called but after the Request Info has been set
     *
     * @param string $action
     * @return void
     */
    public function willInvokeAction($action);

    /**
     * Will be invoked after the actual action method is called
     *
     * @param string                    $action
     * @param ControllerResultInterface $result
     */
    public function didInvokeAction($action, ControllerResultInterface $result);

    /**
     * Process the given request
     *
     * The result output is returned by altering the given response.
     *
     * @param Request  $request
     * @param Response $response The response, modified by this handler
     * @return mixed Returns the result of the processing
     */
    public function processRequest(Request $request, Response $response);
}