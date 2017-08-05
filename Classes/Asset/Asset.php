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

    /**
     * @var
     */
    private $contentType;

    function __construct($uri, $content, $contentType)
    {
        $this->uri = $uri;
        $this->content = $content;
        $this->contentType = $contentType;
    }


    /**
     * @inheritdoc
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @inheritdoc
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @inheritdoc
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
