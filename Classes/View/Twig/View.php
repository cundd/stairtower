<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\View\Twig;


use Cundd\PersistentObjectStore\Configuration\ConfigurationManager;
use Cundd\PersistentObjectStore\Server\ServerInterface;
use Cundd\PersistentObjectStore\View\AbstractView;
use Cundd\PersistentObjectStore\View\Exception\InvalidTemplatePathException;
use Cundd\PersistentObjectStore\View\ExpandableViewInterface;
use Cundd\PersistentObjectStore\View\Twig\Loader\Filesystem;
use Cundd\PersistentObjectStore\View\ViewInterface;
use Twig_Environment;
use Twig_Extension_Debug;
use Twig_Loader_Filesystem;
use Twig_LoaderInterface;
use Twig_SimpleFilter;
use Twig_SimpleFunction;

/**
 * Twig based View
 */
class View extends AbstractView implements ExpandableViewInterface
{
    /**
     * @var Twig_Environment
     */
    protected $engine;

    /**
     * @var Twig_LoaderInterface
     */
    protected $loader;

    /**
     * Render the UI element
     *
     * @return string
     */
    public function render(): string
    {
        if (!$this->templatePath) {
            throw new InvalidTemplatePathException('Template path not defined', 1449572720);
        }
        $development = ConfigurationManager::getSharedInstance()
                ->getConfigurationForKeyPath('serverMode') === ServerInterface::SERVER_MODE_DEVELOPMENT;
        $this->getLoader()->setDisableCache($development);

        return $this->getEngine()->render(basename($this->templatePath), $this->data);
    }

    /**
     * Returns the rendering engine
     *
     * @return Twig_Environment
     */
    public function getEngine()
    {
        if (!$this->engine) {
            $development = ConfigurationManager::getSharedInstance()
                    ->getConfigurationForKeyPath('serverMode') === ServerInterface::SERVER_MODE_DEVELOPMENT;

            $this->engine = new Twig_Environment(
                $this->getLoader(),
                [
                    'debug' => $development,
                    //'cache' => $development ? null : ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('tempPath'),
                ]
            );

            if ($development) {
                $this->engine->addExtension(new Twig_Extension_Debug());
            }
        }

        return $this->engine;
    }

    /**
     * Sets the path to the template
     *
     * @param string $templatePath
     * @return ViewInterface
     */
    public function setTemplatePath(string $templatePath): ViewInterface
    {
        parent::setTemplatePath($templatePath);
        $templateDirectoryPath = dirname($templatePath);

        $loader = $this->getLoader();
        if ($loader instanceof Twig_Loader_Filesystem) {
            if (!in_array($templateDirectoryPath, $loader->getPaths())) {
                $loader->addPath($templateDirectoryPath);
            }
            if (!in_array(dirname($templateDirectoryPath), $loader->getPaths())) {
                $loader->addPath(dirname($templateDirectoryPath));
            }
        }

        return $this;
    }

    /**
     * Returns the loader instance
     *
     * @return Filesystem
     */
    public function getLoader()
    {
        if (!$this->loader) {
            $loaderPaths = [];
            if ($this->templatePath && dirname($this->templatePath)) {
                $loaderPaths = [
                    dirname($this->templatePath),
                    dirname(dirname($this->templatePath)),
                ];
            }
            $this->loader = new Filesystem($loaderPaths);
        }

        return $this->loader;
    }

    public function addFunction(string $key, callable $callback, array $options = []): ExpandableViewInterface
    {
        $this->getEngine()->addFunction(new Twig_SimpleFunction($key, $callback, $options));

        return $this;
    }

    public function addFilter(string $key, callable $callback, array $options = []): ExpandableViewInterface
    {
        $this->getEngine()->addFilter(new Twig_SimpleFilter($key, $callback, $options));

        return $this;
    }

    public function addFilterAndFunction(string $key, callable $callback, array $options = []): ExpandableViewInterface
    {
        $this->addFilter($key, $callback, $options);
        $this->addFunction($key, $callback, $options);

        return $this;
    }
}