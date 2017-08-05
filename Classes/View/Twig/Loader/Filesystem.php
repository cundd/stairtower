<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 22.03.15
 * Time: 10:57
 */

namespace Cundd\PersistentObjectStore\View\Twig\Loader;


use Twig_Loader_Filesystem as BaseFilesystem;

/**
 * Filesystem loader that provides additional features for development
 *
 * @package Cundd\PersistentObjectStore\View\Twig\Loader
 */
class Filesystem extends BaseFilesystem
{
    /**
     * Defines if the caching should be disabled
     *
     * @var bool
     */
    protected $disableCache = false;

    /**
     * Gets the cache key to use for the cache for a given template name.
     *
     * @param  string $name The name of the template to load
     *
     * @return string The cache key
     */
    public function getCacheKey($name)
    {
        $cacheKey = $this->findTemplate($name);
        if ($this->disableCache) {
            return $cacheKey . time();
        }

        return $cacheKey;
    }

    /**
     * Returns true if the template is still fresh.
     *
     * @param string    $name The template name
     * @param \timestamp $time The last modification time of the cached template
     * @return bool
     */
    public function isFresh($name, $time)
    {
        return $this->disableCache || filemtime($this->findTemplate($name)) < $time;
    }

    /**
     * Returns if the caching should be disabled
     *
     * @return boolean
     */
    public function getDisableCache()
    {
        return $this->disableCache;
    }

    /**
     * Sets if the caching should be disabled
     *
     * @param boolean $disableCache
     * @return $this
     */
    public function setDisableCache($disableCache)
    {
        $this->disableCache = $disableCache;

        return $this;
    }


}