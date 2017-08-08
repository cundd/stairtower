<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server\Handler;

/**
 * Interface for classes that describe a Handlers response
 */
interface HandlerResultInterface
{
    /**
     * Returns the status code for the response
     *
     * @return int
     */
    public function getStatusCode(): int;

    /**
     * Returns the request's response data
     *
     * @return mixed
     */
    public function getData();
}