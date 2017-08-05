<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\View;

use Cundd\PersistentObjectStore\Server\Controller\ControllerInterface;
use Cundd\PersistentObjectStore\Server\UriBuilderInterface;

/**
 * Interface for View based controllers
 */
interface ViewControllerInterface extends ControllerInterface
{
    /**
     * Returns the View instance
     *
     * @return \Cundd\PersistentObjectStore\View\ViewInterface
     */
    public function getView();

    /**
     * Returns the template path for the given action
     *
     * @param $action
     * @return string
     */
    public function getTemplatePath($action);

    /**
     * Returns the URI Builder instance
     *
     * @return UriBuilderInterface
     */
    public function getUriBuilder();
}