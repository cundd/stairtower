<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 21.03.15
 * Time: 20:46
 */

namespace Cundd\PersistentObjectStore\View\Twig;


use Cundd\PersistentObjectStore\Configuration\ConfigurationManager;
use Cundd\PersistentObjectStore\Server\ServerInterface;
use Cundd\PersistentObjectStore\View\AbstractView;
use Cundd\PersistentObjectStore\View\ExpandableViewInterface;
use Cundd\PersistentObjectStore\View\Twig\Loader\Filesystem;
use Twig_Environment;
use Twig_Extension_Debug;
use Twig_Loader_Filesystem;
use Twig_LoaderInterface;
use Twig_SimpleFilter;
use Twig_SimpleFunction;

/**
 * Twig based View
 *
 * @package Cundd\PersistentObjectStore\View\Twig
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
    public function render()
    {
        $development = ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('serverMode') === ServerInterface::SERVER_MODE_DEVELOPMENT;
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
            $development = ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('serverMode') === ServerInterface::SERVER_MODE_DEVELOPMENT;

            $this->engine = new Twig_Environment(
                $this->getLoader(),
                array(
                    'debug' => $development,
                    //'cache' => $development ? null : ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('tempPath'),
                )
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
     * @return $this
     */
    public function setTemplatePath($templatePath)
    {
        parent::setTemplatePath($templatePath);
        $templateDirectoryPath = dirname($templatePath);

        $loader = $this->getLoader();
        if ($loader instanceof Twig_Loader_Filesystem && !in_array($templateDirectoryPath, $loader->getPaths())) {
            $loader->addPath($templateDirectoryPath);
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
                    dirname($this->templatePath)
                ];
            }
            $this->loader = new Filesystem($loaderPaths);
        }

        return $this->loader;
    }

    /**
     * Add the function with the given key
     *
     * @param string   $key Key under which the function will be available inside the template
     * @param Callable $callback Callback for this template function
     * @param array    $options Additional options (dependent on the actual view implementation)
     * @return $this
     */
    public function addFunction($key, $callback, array $options = array())
    {
        $this->getEngine()->addFunction(new Twig_SimpleFunction($key, $callback, $options));
        return $this;
    }

    /**
     * Add the filter with the given key
     *
     * @param string   $key      Key under which the filter will be available inside the template
     * @param Callable $callback Callback for this filter
     * @param array    $options  Additional options (dependent on the actual view implementation)
     * @return $this
     */
    public function addFilter($key, $callback, array $options = array())
    {
        $this->getEngine()->addFilter(new Twig_SimpleFilter($key, $callback, $options));
        return $this;
    }
}