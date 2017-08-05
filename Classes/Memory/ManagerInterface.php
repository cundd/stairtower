<?php
declare(strict_types=1);

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
     * @return
     */
    public static function registerObject($object, string $identifier, array $tags = []);

    /**
     * Returns the object for the given identifier or FALSE if it was not found
     *
     * @param string $identifier
     * @return bool|object
     */
    public static function getObject(string $identifier);

    /**
     * Returns if an object for the given identifier is registered
     *
     * @param string $identifier
     * @return bool|object
     */
    public static function hasObject(string $identifier);

    /**
     * Frees the object with the given identifier from the Memory Manager
     *
     * @param string $identifier
     */
    public static function free(string $identifier);


    /**
     * Get identifiers with the given tag
     *
     * @param string $tag
     * @param bool   $graceful
     * @return array
     */
    public static function getIdentifiersByTag(string $tag, bool $graceful = false);

    /**
     * Returns all objects with a given tag
     *
     * @param string $tag
     * @return array
     */
    public static function getObjectsByTag(string $tag);

    /**
     * Free all objects with a given tag
     *
     * @param string $tag
     * @return
     */
    public static function freeObjectsByTag(string $tag);

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