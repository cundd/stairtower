<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 10.10.14
 * Time: 17:59
 */
namespace Cundd\PersistentObjectStore\Server\Handler;

use Cundd\PersistentObjectStore\Server\ValueObject\RequestInfo;

/**
 * Interface for classes that handle the actions from incoming requests
 *
 * @package Cundd\PersistentObjectStore\Server\Handler
 */
interface HandlerInterface
{
    /**
     * Invoked if no route is given (e.g. if the request path is empty)
     *
     * @param RequestInfo $requestInfo
     * @return HandlerResultInterface
     */
    public function noRoute(RequestInfo $requestInfo);

    /**
     * Creates a new Document instance or Database with the given data for the given RequestInfo
     *
     * @param RequestInfo $requestInfo
     * @param mixed       $data
     * @return HandlerResultInterface
     */
    public function create(RequestInfo $requestInfo, $data);

    /**
     * Read Document instances for the given RequestInfo
     *
     * @param RequestInfo $requestInfo
     * @return HandlerResultInterface
     */
    public function read(RequestInfo $requestInfo);

    /**
     * Update a Document instance with the given data for the given RequestInfo
     *
     * @param RequestInfo $requestInfo
     * @param mixed       $data
     * @return HandlerResultInterface
     */
    public function update(RequestInfo $requestInfo, $data);

    /**
     * Deletes a Document instance for the given RequestInfo
     *
     * @param RequestInfo $requestInfo
     * @return HandlerResultInterface
     */
    public function delete(RequestInfo $requestInfo);

    /**
     * Action to display server statistics
     *
     * @param RequestInfo $requestInfo
     * @return HandlerResultInterface
     */
    public function getStatsAction(RequestInfo $requestInfo);

    /**
     * Action to display all databases
     *
     * @param RequestInfo $requestInfo
     * @return HandlerResultInterface
     */
    public function getAllDbsAction(RequestInfo $requestInfo);

    /**
     * Returns the count of the result set
     *
     * @param RequestInfo $requestInfo
     * @return HandlerResultInterface
     */
    public function getCountAction(RequestInfo $requestInfo);

    /**
     * Returns the description of a database
     *
     * @param RequestInfo $requestInfo
     * @return HandlerResultInterface
     */
    public function getDescribeAction(RequestInfo $requestInfo);
}