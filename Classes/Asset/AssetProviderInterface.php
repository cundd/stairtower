<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Asset;

/**
 * Interface for Asset Providers
 */
interface AssetProviderInterface
{
    /**
     * Returns if an Asset for the given URI exists
     *
     * @param string $uri
     * @return bool
     */
    public function hasAssetForUri(string $uri):bool;

    /**
     * Returns the Asset for the given URI
     *
     * @param string $uri
     * @param bool   $noCache
     * @return AssetInterface|null
     */
    public function getAssetForUri(string $uri, bool $noCache = false): ?AssetInterface;
}