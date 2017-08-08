<?php


namespace Cundd\PersistentObjectStore\Filter\Comparison;

/**
 * Filter Comparison that will invoke a user defined callback
 */
class CallableComparison implements ComparisonInterface
{
    private $callback;

    /**
     * @param callable $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function perform($testValue): bool
    {
        $impl = $this->callback;

        return (bool)$impl($testValue);
    }

    public function getOperator(): string
    {
        return ComparisonInterface::TYPE_EQUAL_TO;
    }
}
