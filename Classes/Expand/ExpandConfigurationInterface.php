<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 29.12.14
 * Time: 15:48
 */

namespace Cundd\PersistentObjectStore\Expand;

/**
 * Interface for Expand configurations
 *
 * @package Cundd\PersistentObjectStore\Expand
 */
interface ExpandConfigurationInterface
{
    /**
     * Returns the identifier of the (foreign) Database
     *
     * @return string
     */
    public function getDatabaseIdentifier();

    /**
     * Returns the foreign property key
     *
     * @return string
     */
    public function getForeignKey();

    /**
     * Returns the local property key
     *
     * @return string
     */
    public function getLocalKey();

    /**
     * Returns the "as" property key
     *
     * If defined this property key will be filled with the foreign data
     *
     * @return string
     */
    public function getAsKey();

    /**
     * Returns if the property will have more than one connected Document
     *
     * @return bool
     */
    public function getExpandToMany();
}
