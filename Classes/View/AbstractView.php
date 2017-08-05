<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\View;

/**
 * Abstract View class
 */
abstract class AbstractView implements ViewInterface
{
    /**
     * Data
     *
     * @var array
     */
    protected $data = [];

    /**
     * @var string
     */
    protected $templatePath = '';

    /**
     * Assign value for variable key
     *
     * @param string $key
     * @param mixed  $value
     * @return ViewInterface
     */
    public function assign(string $key, $value): ViewInterface
    {
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * Assign multiple values
     *
     * @param array $values
     * @return ViewInterface
     */
    public function assignMultiple(array $values): ViewInterface
    {
        $this->data = array_merge($this->data, (array)$values);

        return $this;
    }

    /**
     * Sets the path to the template
     *
     * @param string $templatePath
     * @return ViewInterface
     */
    public function setTemplatePath(string $templatePath): ViewInterface
    {
        $this->templatePath = $templatePath;

        return $this;
    }

    /**
     * Returns the path to the template
     *
     * @return string
     */
    public function getTemplatePath(): string
    {
        return (string)$this->templatePath;
    }

    function __toString()
    {
        return $this->render();
    }


}