<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Server\ValueObject;


use Cundd\PersistentObjectStore\Immutable;
use Cundd\PersistentObjectStore\Server\Handler\HandlerResultInterface;

/**
 * Class to signal deferred results implementation
 */
class DeferredResult implements HandlerResultInterface, Immutable
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
     * @return int
     */
    public function getStatusCode(): int
    {
        return 204;
    }

    /**
     * Returns the singleton instance
     *
     * @return DeferredResult
     */
    public static function instance()
    {
        static $instance;
        if (!$instance) {
            $instance = new static();
        }

        return $instance;
    }
}
