<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 08.04.15
 * Time: 20:00
 */

namespace Cundd\PersistentObjectStore\Asset;

/**
 * Asset implementation
 *
 * @package Cundd\PersistentObjectStore\Asset
 */
class Asset implements AssetInterface
{
    /**
     * @var string
     */
    protected $uri;

    /**
     * @var string
     */
    protected $content;

    function __construct($uri, $content)
    {
        $this->uri     = $uri;
        $this->content = $content;
    }


    /**
     * Returns the URI
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Returns the content of the asset
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    function __toString()
    {
        return (string)$this->getContent();
    }


}