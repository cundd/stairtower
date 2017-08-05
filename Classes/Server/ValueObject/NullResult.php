<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Server\ValueObject;


use Cundd\PersistentObjectStore\Immutable;
use Cundd\PersistentObjectStore\Server\Handler\HandlerResultInterface;

/**
 * Empty result implementation
 */
class NullResult implements HandlerResultInterface, Immutable
{
    /**
     * Returns the request's response data
     *
     * @return mixed
     */
    public function getData()
    {
        return null;
    }

    /**
     * Returns the status code for the response
     *
     * @return integer
     */
    public function getStatusCode()
    {
        return 204;
    }
}
