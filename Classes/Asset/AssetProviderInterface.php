<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 08.04.15
 * Time: 19:42
 */

namespace Cundd\PersistentObjectStore\Asset;

/**
 * Interface for Asset Providers
 *
 * @package Cundd\PersistentObjectStore\Asset
 */
interface AssetProviderInterface {
    /**
     * Returns if an Asset for the given URI exists
     *
     * @param string $uri
     * @return bool
     */
    public function hasAssetForUri($uri);

    /**
     * Returns the Asset for the given URI
     *
     * @param string $uri
     * @param bool   $noCache
     * @return AssetInterface|null
     */
    public function getAssetForUri($uri, $noCache = false);
}