<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\View;

/**
 * Interface for views that allow adding additional filters and functions
 */
interface ExpandableViewInterface
{
    /**
     * Add the function with the given key
     *
     * @param string   $key      Key under which the function will be available inside the template
     * @param Callable $callback Callback for this template function
     * @param array    $options  Additional options (dependent on the actual view implementation)
     * @return ExpandableViewInterface
     */
    public function addFunction(string $key, callable $callback, array $options = []): ExpandableViewInterface;

    /**
     * Add the filter with the given key
     *
     * @param string   $key      Key under which the filter will be available inside the template
     * @param Callable $callback Callback for this filter
     * @param array    $options  Additional options (dependent on the actual view implementation)
     * @return ExpandableViewInterface
     */
    public function addFilter(string $key, callable $callback, array $options = []): ExpandableViewInterface;

    /**
     * Add the given key and callback as filter and as function
     *
     * @param string   $key      Key under which the filter and function will be available inside the template
     * @param Callable $callback Callback
     * @param array    $options  Additional options (dependent on the actual view implementation)
     * @return ExpandableViewInterface
     */
    public function addFilterAndFunction(string $key, callable $callback, array $options = []): ExpandableViewInterface;
}