<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 21.03.15
 * Time: 20:35
 */

namespace Cundd\PersistentObjectStore\View;

/**
 * Interface for views that allow adding additional filters and functions
 *
 * @package Cundd\PersistentObjectStore\View
 */
interface ExpandableViewInterface
{
    /**
     * Add the function with the given key
     *
     * @param string   $key Key under which the function will be available inside the template
     * @param Callable $callback Callback for this template function
     * @param array    $options Additional options (dependent on the actual view implementation)
     * @return $this
     */
    public function addFunction($key, $callback, array $options = array());

    /**
     * Add the filter with the given key
     *
     * @param string   $key Key under which the filter will be available inside the template
     * @param Callable $callback Callback for this filter
     * @param array    $options Additional options (dependent on the actual view implementation)
     * @return $this
     */
    public function addFilter($key, $callback, array $options = array());
}