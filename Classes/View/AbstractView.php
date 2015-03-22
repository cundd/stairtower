<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 21.03.15
 * Time: 20:38
 */

namespace Cundd\PersistentObjectStore\View;

/**
 * Abstract View class
 *
 * @package Cundd\PersistentObjectStore\View
 */
abstract class AbstractView implements ViewInterface
{
    /**
     * Data
     *
     * @var array
     */
    protected $data = array();

    /**
     * @var string
     */
    protected $templatePath = '';

    /**
     * Assign value for variable key
     *
     * @param string $key
     * @param mixed  $value
     * @return $this
     */
    public function assign($key, $value)
    {
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * Assign multiple values
     *
     * @param array $values
     * @return $this
     */
    public function assignMultiple($values)
    {
        $this->data = array_merge($this->data, (array)$values);
    }

    /**
     * Sets the path to the template
     *
     * @param string $templatePath
     * @return $this
     */
    public function setTemplatePath($templatePath)
    {
        $this->templatePath = $templatePath;

        return $this;
    }

    /**
     * Returns the path to the template
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    function __toString()
    {
        return $this->render();
    }


}