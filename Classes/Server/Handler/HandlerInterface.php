<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Server\Handler;

use Cundd\PersistentObjectStore\Server\ValueObject\Request;
use Cundd\PersistentObjectStore\Server\ValueObject\RequestInterface;

/**
 * Interface for classes that handle the actions from incoming requests
 */
interface HandlerInterface
{
    /**
     * Invoked if no route is given (e.g. if the request path is empty)
     *
     * @param RequestInterface $request
     * @return HandlerResultInterface
     */
    public function noRoute(RequestInterface $request);

    /**
     * Creates a new Document instance or Database with the given data for the given Request
     *
     * @param RequestInterface $request
     * @param mixed            $data
     * @return HandlerResultInterface
     */
    public function create(RequestInterface $request, $data);

    /**
     * Read Document instances for the given Request
     *
     * @param RequestInterface $request
     * @return HandlerResultInterface
     */
    public function read(RequestInterface $request);

    /**
     * Update a Document instance with the given data for the given Request
     *
     * @param RequestInterface $request
     * @param mixed            $data
     * @return HandlerResultInterface
     */
    public function update(RequestInterface $request, $data);

    /**
     * Deletes a Document instance for the given Request
     *
     * @param RequestInterface $request
     * @return HandlerResultInterface
     */
    public function delete(RequestInterface $request);

    /**
     * Action to display server statistics
     *
     * @param RequestInterface|Request $request
     * @return HandlerResultInterface
     */
    public function getStatsAction(RequestInterface $request);

    /**
     * Action to deliver assets
     *
     * @param RequestInterface $request
     * @return HandlerResultInterface
     */
    public function getAssetAction(RequestInterface $request);

    /**
     * Action to display all databases
     *
     * @param RequestInterface $request
     * @return HandlerResultInterface
     */
    public function getAllDbsAction(RequestInterface $request);

    /**
     * Returns the count of the result set
     *
     * @param RequestInterface $request
     * @return HandlerResultInterface
     */
    public function getCountAction(RequestInterface $request);
}