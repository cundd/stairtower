<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 22.03.15
 * Time: 11:26
 */

namespace Cundd\PersistentObjectStore\View;


use Cundd\PersistentObjectStore\Configuration\ConfigurationManager;
use Cundd\PersistentObjectStore\Server\UriBuilderInterface;

/**
 * Trait for View base controllers
 *
 * @package Cundd\PersistentObjectStore\View
 */
trait ViewControllerTrait
{
    /**
     * @var \Cundd\PersistentObjectStore\View\ViewInterface
     */
    protected $view;

    /**
     * Path pattern for templates
     *
     * @var string
     */
    protected $templatePathPattern = '%sTemplate/%s/%s.twig';

    /**
     * Class name of the View implementation
     *
     * @var string
     */
    protected $viewClass = 'Cundd\\PersistentObjectStore\\View\\Twig\\View';

    /**
     * @var \Cundd\PersistentObjectStore\Server\UriBuilderInterface
     * @Inject
     */
    protected $uriBuilder;

    /**
     * Returns the View instance
     *
     * @return \Cundd\PersistentObjectStore\View\ViewInterface
     */
    public function getView()
    {
        if (!$this->view) {
            $viewClass  = $this->viewClass;
            $this->view = new $viewClass();

            $this->initializeViewAdditions();
        }

        return $this->view;
    }

    /**
     * Returns the URI Builder instance
     *
     * @return UriBuilderInterface
     */
    public function getUriBuilder()
    {
        return $this->uriBuilder;
    }

    /**
     * Returns the template path for the given action
     *
     * @param $action
     * @return string
     */
    public function getTemplatePath($action)
    {
        $basePath = ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('privateResources');

        // Strip 'Action'
        $templateIdentifier  = substr($action, 0, -6);
        $controllerNamespace = $this->getUriBuilder()->getControllerNamespaceForController($this);
        $controllerName      = substr(strrchr($controllerNamespace, UriBuilderInterface::CONTROLLER_NAME_SEPARATOR), 1);
        $templatePath = sprintf($this->templatePathPattern, $basePath, $controllerName, $templateIdentifier);

        return $templatePath;
    }

    /**
     * Initialize the additional filters of expandable views
     */
    protected function initializeViewAdditions()
    {
        if ($this->view instanceof ExpandableViewInterface) {
            $this->view->addFunction('action',
                function ($actionName, $controller = null, $database = null, $document = null) {
                    if ($controller === null) {
                        $controller = $this;
                    }

                    return $this->getUriBuilder()->buildUriFor($actionName, $controller, $database, $document);
                });
        }
    }

}