<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server\Controller;

use Cundd\Stairtower\Server\Exception\InvalidRequestActionException;
use Cundd\Stairtower\Server\Exception\RequestMethodNotImplementedException;
use Cundd\Stairtower\Server\Handler\HandlerResultInterface;
use Cundd\Stairtower\Server\ValueObject\ControllerResult;
use Cundd\Stairtower\Server\ValueObject\Request;
use Cundd\Stairtower\Server\ValueObject\RequestInterface;
use Cundd\Stairtower\View\ViewControllerInterface;

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

    public function didInvokeAction(string $action, ControllerResultInterface $result): void
    {
    }

    public function processRequest(RequestInterface $request): ControllerResultInterface
    {
        if (!$request->getAction()) {
            throw new InvalidRequestActionException(
                sprintf('Request action must not be empty'),
                1420551045
            );
        }
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
        return $request->getParsedBody();
    }
}
