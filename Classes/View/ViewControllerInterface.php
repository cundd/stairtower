<?php
declare(strict_types=1);

namespace Cundd\Stairtower\View;

use Cundd\Stairtower\Server\Controller\ControllerInterface;
use Cundd\Stairtower\Server\UriBuilderInterface;

/**
 * Interface for View based controllers
 */
interface ViewControllerInterface extends ControllerInterface
{
    /**
     * Returns the View instance
     *
     * @return \Cundd\Stairtower\View\ViewInterface
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

    /**
     * Perform actions to prepare the View for handling the next request
     *
     * @return void
     */
    public function resetView();
}
