<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Server\Handler;

/**
 * Interface for classes that describe a Handlers response
 */
interface HandlerResultInterface
{
    /**
     * Returns the status code for the response
     *
     * @return integer
     */
    public function getStatusCode();

    /**
     * Returns the request's response data
     *
     * @return mixed
     */
    public function getData();
}