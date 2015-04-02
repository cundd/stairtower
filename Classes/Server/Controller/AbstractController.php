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
use Cundd\PersistentObjectStore\Server\ValueObject\RequestInfo as Request;
use Cundd\PersistentObjectStore\View\ViewControllerInterface;
use React\Http\Response;

/**
 * An abstract Controller implementation
 *
 * @package Cundd\PersistentObjectStore\Server\Controller
 */
abstract class AbstractController implements ControllerInterface
{
    /**
     * @var Request
     */
    protected $request;

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
     * @param Request $request
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Returns the current Request Info instance
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Unset the Request Info instance after the request has been processed
     *
     * This method will be called to free the Request Info instance
     *
     * @return void
     */
    public function unsetRequest()
    {
        $this->request = null;
    }

    /**
     * Will be invoked before the actual action method is called but after the Request Info has been set
     *
     * @param string $action
     * @return void
     */
    public function willInvokeAction($action)
    {
        if ($this instanceof ViewControllerInterface) {
            $templatePath = $this->getTemplatePath($action);
            $this->getView()->setTemplatePath($templatePath);
            $this->getView()->assignMultiple(array(
                'appNamespace' => $this->getUriBuilder()->getControllerNamespaceForController($this),
                'action'       => $this->getRequest()->getActionName(),
            ));
        }
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
     * @param Request  $request
     * @param Response $response The response, modified by this handler
     * @return mixed Returns the result of the processing
     */
    public function processRequest(Request $request, Response $response) {
        if (!method_exists($this, $request->getAction())) {
            throw new RequestMethodNotImplementedException(
                sprintf('Request method %s is not defined', $request->getAction()),
                1420551044
            );
        }

        $action = $request->getAction();

        // Prepare the Controller for the current request
        $this->initialize();
        $this->setRequest($request);
        $this->willInvokeAction($action);

        $argument = $this->prepareArgumentForRequestAndAction($request, $action, $noArgument);

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
        $this->unsetRequest();

        return $result;
    }

    /**
     * Returns the argument to be passed to the action
     *
     * @param Request $request Request info object
     * @param string $action Action name
     * @param bool $noArgument Reference the will be set to true if no argument should be passed
     * @return mixed
     */
    protected function prepareArgumentForRequestAndAction($request, $action, &$noArgument = false)
    {
        return $request->getBody();
    }
}