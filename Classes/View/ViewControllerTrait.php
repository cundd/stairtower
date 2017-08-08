<?php
declare(strict_types=1);

namespace Cundd\Stairtower\View;


use Cundd\Stairtower\Configuration\ConfigurationManager;
use Cundd\Stairtower\Server\UriBuilderInterface;
use Cundd\Stairtower\View\Twig\View;

/**
 * Trait for View base controllers
 */
trait ViewControllerTrait
{
    /**
     * @var \Cundd\Stairtower\View\ViewInterface
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
    protected $viewClass = View::class;

    /**
     * @var \Cundd\Stairtower\Server\UriBuilderInterface
     * @Inject
     */
    protected $uriBuilder;

    /**
     * Returns the View instance
     *
     * @return \Cundd\Stairtower\View\ViewInterface
     */
    public function getView()
    {
        if (!$this->view) {
            $viewClass = $this->viewClass;
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
     * Sets the URI Builder instance
     *
     * @param UriBuilderInterface $uriBuilder
     */
    public function setUriBuilder(UriBuilderInterface $uriBuilder)
    {
        $this->uriBuilder = $uriBuilder;
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
        $templateIdentifier = substr($action, 0, -6);
        $controllerNamespace = $this->getUriBuilder()->getControllerNamespaceForController($this);
        $controllerName = ucfirst(
            substr(strrchr($controllerNamespace, UriBuilderInterface::CONTROLLER_NAME_SEPARATOR), 1)
        );
        $templatePath = sprintf($this->templatePathPattern, $basePath, $controllerName, $templateIdentifier);

        return $templatePath;
    }

    /**
     * Initialize the additional filters of expandable views
     */
    protected function initializeViewAdditions()
    {
        if ($this->view instanceof ExpandableViewInterface) {
            $this->view->addFilterAndFunction(
                'action',
                function (
                    string $action,
                    $controller = null,
                    $database = null,
                    $document = null,
                    array $query = [],
                    $fragment = ''
                ) {
                    if ($controller === null) {
                        $controller = $this;
                    }

                    return $this->getUriBuilder()->buildUriFor(
                        $action,
                        $controller,
                        $database,
                        $document,
                        $query,
                        (string)$fragment
                    );
                }
            );
            $this->view->addFilterAndFunction(
                'assetUri',
                function (string $assetUri, $noCache = false) {
                    $uri = '/_asset/' . ltrim($assetUri);
                    if ($noCache) {
                        return $uri . '?v=' . time();
                    }

                    return $uri;
                }
            );
        }
    }

}