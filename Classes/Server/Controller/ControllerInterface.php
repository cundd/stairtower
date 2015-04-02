<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 08.01.15
 * Time: 10:40
 */

namespace Cundd\PersistentObjectStore\Server\Controller;

use Cundd\PersistentObjectStore\Server\ValueObject\RequestInfo;
use React\Http\Request;
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
     * @param RequestInfo $requestInfo
     * @return $this
     */
    public function setRequestInfo(RequestInfo $requestInfo);

    /**
     * Returns the current Request Info instance
     *
     * @return RequestInfo
     */
    public function getRequestInfo();

    /**
     * Unset the Request Info instance after the request has been processed
     *
     * This method will be called to free the Request Info instance
     *
     * @return void
     */
    public function unsetRequestInfo();

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
     * @param RequestInfo $requestInfo The request object
     * @param Response $response The response, modified by this handler
     *
     * @return mixed Returns the result of the processing
     */
    public function processRequest(RequestInfo $requestInfo, Response $response);
}