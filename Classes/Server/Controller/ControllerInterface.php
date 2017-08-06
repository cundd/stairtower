<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Server\Controller;

use Cundd\PersistentObjectStore\Server\ValueObject\RequestInterface;
use React\Stream\WritableStreamInterface;

/**
 * Interface for Controllers
 */
interface ControllerInterface
{
    /**
     * Initialize the Controller instance
     *
     * This method will be called first in the request handling
     */
    public function initialize(): void;

    /**
     * Sets the Request Info for the current request
     *
     * @param RequestInterface $request
     * @return $this
     */
    public function setRequest(RequestInterface $request): ControllerInterface;

    /**
     * Returns the current Request Info instance
     *
     * @return RequestInterface
     */
    public function getRequest(): ?RequestInterface;

    /**
     * Unset the Request Info instance after the request has been processed
     *
     * This method will be called to free the Request Info instance
     *
     * @return void
     */
    public function unsetRequest(): void;

    /**
     * Will be invoked before the actual action method is called but after the Request Info has been set
     *
     * @param string $action
     * @return void
     */
    public function willInvokeAction(string $action): void;

    /**
     * Will be invoked after the actual action method is called
     *
     * @param string                    $action
     * @param ControllerResultInterface $result
     */
    public function didInvokeAction(string $action, ControllerResultInterface $result): void;

    /**
     * Process the given request
     *
     * The result output is returned by altering the given response.
     *
     * @param RequestInterface        $request
     * @param WritableStreamInterface $response The response, modified by this handler
     * @return ControllerResultInterface Returns the result of the processing
     */
    public function processRequest(
        RequestInterface $request,
        WritableStreamInterface $response
    ): ControllerResultInterface;
}