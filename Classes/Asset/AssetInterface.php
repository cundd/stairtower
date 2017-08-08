<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Asset;

/**
 * Interface for Assets
 */
interface AssetInterface
{
    /**
     * Returns the URI
     *
     * @return string
     */
    public function getUri(): string;

    /**
     * Returns the content of the asset
     *
     * @return string
     */
    public function getContent(): string;

    /**
     * Returns the content type for the asset
     *
     * @return string
     */
    public function getContentType(): string;
}
