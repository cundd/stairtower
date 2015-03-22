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
trait ViewControllerTrait {
    /**
     * @var \Cundd\PersistentObjectStore\View\ViewInterface
     */
    protected $view;

    /**
     * Class name of the View implementation
     *
     * @var string
     */
    protected $viewClass = 'Cundd\\PersistentObjectStore\\View\\Twig\\View';

    /**
     * @var UriBuilderInterface
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
            $viewClass = $this->viewClass;
            $this->view = new $viewClass();
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
        $basePath           = ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('basePath');
        $templateIdentifier = substr($action, 0, -6);
        $templatePath       = sprintf('%sResources/Private/Template/%s.twig', $basePath, $templateIdentifier);

        return $templatePath;
    }

}