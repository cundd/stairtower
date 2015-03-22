<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 21.03.15
 * Time: 20:35
 */

namespace Cundd\PersistentObjectStore\View;

/**
 * Interface for views
 *
 * @package Cundd\PersistentObjectStore\View
 */
interface ViewInterface
{
    /**
     * Render the UI element
     *
     * @return string
     */
    public function render();

    /**
     * Assign value for variable key
     *
     * @param string $key
     * @param mixed  $value
     * @return $this
     */
    public function assign($key, $value);

    /**
     * Assign multiple values
     *
     * @param array $values
     * @return $this
     */
    public function assignMultiple($values);

    /**
     * Returns the path to the template
     *
     * @return string
     */
    public function getTemplatePath();

    /**
     * Sets the path to the template
     *
     * @param string $templatePath
     * @return $this
     */
    public function setTemplatePath($templatePath);
}