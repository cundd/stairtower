<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 08.01.15
 * Time: 11:04
 */

namespace Cundd\PersistentObjectStore\Server\Controller;

use Cundd\PersistentObjectStore\Server\Exception\RequestMethodNotImplementedException;
use Cundd\PersistentObjectStore\Server\Handler\HandlerResultInterface;
use Cundd\PersistentObjectStore\Server\ValueObject\ControllerResult;
use Cundd\PersistentObjectStore\Server\ValueObject\RequestInfo;
use React\Http\Request;
use React\Http\Response;

/**
 * An abstract Controller implementation
 *
 * @package Cundd\PersistentObjectStore\Server\Controller
 */
abstract class AbstractController implements ControllerInterface
{
    /**
     * @var RequestInfo
     */
    protected $requestInfo;

    /**
     * Initialize the Controller instance
     *
     * This method will be called first in the request handling
     *
     * @return void
     */
    public function initialize()
    {
    }

    /**
     * Sets the Request Info for the current request
     *
     * @param RequestInfo $requestInfo
     * @return $this
     */
    public function setRequestInfo(RequestInfo $requestInfo)
    {
        $this->requestInfo = $requestInfo;
        return $this;
    }

    /**
     * Returns the current Request Info instance
     *
     * @return RequestInfo
     */
    public function getRequestInfo()
    {
        return $this->requestInfo;
    }

    /**
     * Unset the Request Info instance after the request has been processed
     *
     * This method will be called to free the Request Info instance
     *
     * @return void
     */
    public function unsetRequestInfo()
    {
        $this->requestInfo = null;
    }

    /**
     * Returns the current Request
     *
     * @return Request
     */
    public function getRequest()
    {
        if (!$this->requestInfo) {
            return null;
        }
        return $this->requestInfo->getRequest();
    }

    /**
     * Will be invoked before the actual action method is called but after the Request Info has been set
     *
     * @param string $action
     * @return void
     */
    public function willInvokeAction($action)
    {
    }

    /**
     * Will be invoked after the actual action method is called
     *
     * @param string                    $action
     * @param ControllerResultInterface $result
     */
    public function didInvokeAction($action, ControllerResultInterface $result)
    {
    }

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
    public function processRequest(RequestInfo $requestInfo, Response $response) {
        if (!method_exists($this, $requestInfo->getAction())) {
            throw new RequestMethodNotImplementedException(
                sprintf('Request method %s is not defined', $requestInfo->getAction()),
                1420551044
            );
        }

        $action = $requestInfo->getAction();

        // Prepare the Controller for the current request
        $this->initialize();
        $this->setRequestInfo($requestInfo);
        $this->willInvokeAction($action);

        $argument = $this->prepareArgumentForRequestAndAction($requestInfo, $action, $noArgument);

        // Invoke the Controller action
        if (!$noArgument) {
            $rawResult = $this->$action($argument);
        } else {
            $rawResult = $this->$action();
        }

        // Transform the raw result into a Controller Result if needed
        if ($rawResult instanceof ControllerResultInterface) {
            $result = $rawResult;
        } elseif ($rawResult instanceof HandlerResultInterface) {
            $result = new ControllerResult(
                $rawResult->getStatusCode(),
                $rawResult->getData()
            );
        } else {
            $result = new ControllerResult(200, $rawResult);
        }
        $this->didInvokeAction($action, $result);
        $this->unsetRequestInfo();

        return $result;
    }

    /**
     * Returns the argument to be passed to the action
     *
     * @param RequestInfo $requestInfo Request info object
     * @param string $action Action name
     * @param bool $noArgument Reference the will be set to true if no argument should be passed
     * @return mixed
     */
    protected function prepareArgumentForRequestAndAction($requestInfo, $action, &$noArgument = false)
    {
        return $requestInfo->getBody();
    }
}