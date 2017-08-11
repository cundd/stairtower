<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server\Dispatcher;

/**
 * Wrapper for the resolve and reject callbacks of a Promise
 */
class PromiseCallback
{
    /**
     * @var callable
     */
    private $resolve;

    /**
     * @var callable
     */
    private $reject;

    /**
     * PromiseCallback constructor.
     *
     * @param callable $resolve
     * @param callable $reject
     */
    public function __construct(callable $resolve, callable $reject)
    {
        $this->resolve = $resolve;
        $this->reject = $reject;
    }

    /**
     * Invoke the resolve callback
     *
     * @param array ...$arguments
     */
    public function resolve(...$arguments)
    {
        $callback = $this->resolve;
        $callback(...$arguments);
    }

    /**
     * Invoke the reject callback
     *
     * @param array ...$arguments
     */
    public function reject(...$arguments)
    {
        $callback = $this->reject;
        $callback(...$arguments);
    }
}
