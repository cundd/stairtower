<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Asset;

/**
 * Asset implementation
 */
class Asset implements AssetInterface
{
    /**
     * @var string
     */
    private $uri;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $contentType;

    function __construct(string $uri, string $content, string $contentType)
    {
        $this->uri = $uri;
        $this->content = $content;
        $this->contentType = $contentType;
    }


    public function getUri(): string
    {
        return $this->uri;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    function __toString()
    {
        return (string)$this->getContent();
    }
}
