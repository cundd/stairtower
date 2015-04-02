<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 10.10.14
 * Time: 17:59
 */
namespace Cundd\PersistentObjectStore\Server\Handler;

use Cundd\PersistentObjectStore\Server\ValueObject\RequestInfo as Request;

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
     * @param Request $request
     * @return HandlerResultInterface
     */
    public function noRoute(Request $request);

    /**
     * Creates a new Document instance or Database with the given data for the given Request
     *
     * @param Request $request
     * @param mixed   $data
     * @return HandlerResultInterface
     */
    public function create(Request $request, $data);

    /**
     * Read Document instances for the given Request
     *
     * @param Request $request
     * @return HandlerResultInterface
     */
    public function read(Request $request);

    /**
     * Update a Document instance with the given data for the given Request
     *
     * @param Request $request
     * @param mixed   $data
     * @return HandlerResultInterface
     */
    public function update(Request $request, $data);

    /**
     * Deletes a Document instance for the given Request
     *
     * @param Request $request
     * @return HandlerResultInterface
     */
    public function delete(Request $request);

    /**
     * Action to display server statistics
     *
     * @param Request $request
     * @return HandlerResultInterface
     */
    public function getStatsAction(Request $request);

    /**
     * Action to display all databases
     *
     * @param Request $request
     * @return HandlerResultInterface
     */
    public function getAllDbsAction(Request $request);

    /**
     * Returns the count of the result set
     *
     * @param Request $request
     * @return HandlerResultInterface
     */
    public function getCountAction(Request $request);
}