<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Server\Controller;

use Cundd\PersistentObjectStore\Server\Exception\RequestMethodNotImplementedException;
use Cundd\PersistentObjectStore\Server\Handler\HandlerResultInterface;
use Cundd\PersistentObjectStore\Server\ValueObject\ControllerResult;
use Cundd\PersistentObjectStore\Server\ValueObject\Request;
use Cundd\PersistentObjectStore\Server\ValueObject\RequestInterface;
use Cundd\PersistentObjectStore\View\ViewControllerInterface;
use React\Stream\WritableStreamInterface;

/**
 * An abstract Controller implementation
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
    public function initialize(): void
    {
    }

    public function setRequest(RequestInterface $request): ControllerInterface
    {
        $this->request = $request;

        return $this;
    }

    public function getRequest(): ?RequestInterface
    {
        return $this->request;
    }

    public function unsetRequest(): void
    {
        $this->request = null;
    }

    public function willInvokeAction(string $action): void
    {
        if ($this instanceof ViewControllerInterface) {
            $templatePath = $this->getTemplatePath($action);
            $this->getView()->setTemplatePath($templatePath);
            $this->getView()->assignMultiple(
                [
                    'appNamespace' => $this->getUriBuilder()->getControllerNamespaceForController($this),
                    'action'       => $this->getRequest()->getActionName(),
                ]
            );
        }
    }

    public function didInvokeAction(string $action, ControllerResultInterface $result)
    {
    }

    public function processRequest(RequestInterface $request, WritableStreamInterface $response)
    {
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
     * @param RequestInterface $request    Request info object
     * @param string           $action     Action name
     * @param bool             $noArgument Reference the will be set to true if no argument should be passed
     * @return mixed
     */
    protected function prepareArgumentForRequestAndAction(
        RequestInterface $request,
        string $action,
        &$noArgument = false
    ) {
        return $request->getBody();
    }
}