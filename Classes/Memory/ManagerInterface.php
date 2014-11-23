<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 18.10.14
 * Time: 13:40
 */

namespace Cundd\PersistentObjectStore\Memory;


use Cundd\PersistentObjectStore\Memory\Exception\ManagerException;

interface ManagerInterface
{
    /**
     * Register the given object for the given identifier
     *
     * @param object $object
     * @param string $identifier
     * @param array  $tags
     */
    public static function registerObject($object, $identifier, $tags = array());

    /**
     * Returns the object for the given identifier or FALSE if it was not found
     *
     * @param string $identifier
     * @return object|bool
     */
    public static function getObject($identifier);

    /**
     * Returns if an object for the given identifier is registered
     *
     * @param string $identifier
     * @return object|bool
     */
    public static function hasObject($identifier);

    /**
     * Frees the object with the given identifier from the Memory Manager
     *
     * @param string $identifier
     * @throws ManagerException if no object for the given identifier is registered
     */
    public static function free($identifier);


    /**
     * Get identifiers with the given tag
     *
     * @param string $tag
     * @param bool   $graceful
     * @return array
     * @throws ManagerException if the given tag is not found an graceful is FALSE
     */
    public static function getIdentifiersByTag($tag, $graceful = false);

    /**
     * Returns all objects with a given tag
     *
     * @param string $tag
     * @return array
     */
    public static function getObjectsByTag($tag);

    /**
     * Free all objects with a given tag
     *
     * @param string $tag
     * @return array
     */
    public static function freeObjectsByTag($tag);

    /**
     * Frees all managed objects
     *
     * @internal
     */
    public static function freeAll();

    /**
     * Tells the Memory Manager to clean up the memory
     */
    public static function cleanup();
}