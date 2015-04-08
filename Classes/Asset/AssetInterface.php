<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 08.04.15
 * Time: 19:44
 */

namespace Cundd\PersistentObjectStore\Asset;

/**
 * Interface for Assets
 *
 * @package Cundd\PersistentObjectStore\Asset
 */
interface AssetInterface
{
    /**
     * Returns the URI
     *
     * @return string
     */
    public function getUri();

    /**
     * Returns the content of the asset
     *
     * @return string
     */
    public function getContent();
}