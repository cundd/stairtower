<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\View;

/**
 * Interface for views
 */
interface ViewInterface
{
    /**
     * Render the UI element
     *
     * @return string
     */
    public function render(): string;

    /**
     * Assign value for variable key
     *
     * @param string $key
     * @param mixed  $value
     * @return ViewInterface
     */
    public function assign(string $key, $value): ViewInterface;

    /**
     * Assign multiple values
     *
     * @param array $values
     * @return ViewInterface
     */
    public function assignMultiple(array $values): ViewInterface;

    /**
     * Returns the path to the template
     *
     * @return string
     */
    public function getTemplatePath(): string;

    /**
     * Sets the path to the template
     *
     * @param string $templatePath
     * @return ViewInterface
     */
    public function setTemplatePath(string $templatePath): ViewInterface;
}