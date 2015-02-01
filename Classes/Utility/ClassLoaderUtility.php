<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 25.08.14
 * Time: 21:40
 */

namespace Cundd\PersistentObjectStore\Utility;

/**
 * Class loader middleware to cache class loading results
 *
 * @package Cundd\PersistentObjectStore\Utility
 */
abstract class ClassLoaderUtility
{
    /**
     * Collection of existing classes
     *
     * @var array
     */
    protected static $existingClasses;

    /**
     * Returns if the given class exists
     *
     * @param string $className
     * @param bool   $cached
     * @return bool
     */
    public static function classExists($className, $cached = true)
    {
        // Collect the already existing classes
        if (!static::$existingClasses) {
            self::loadDeclaredClasses();
        }

        $className = strtolower($className);
        if ($cached) {
            return isset(static::$existingClasses[$className]);
        }
        $result = class_exists($className, true);
        if (!$result) {
            return false;
        }
        static::$existingClasses[$className] = true;
        return true;
    }

    /**
     * Clears the class cache
     */
    public static function clearClassCache()
    {
        static::$existingClasses = null;
    }

    /**
     * Loads the already declared classes
     */
    protected static function loadDeclaredClasses()
    {
        $declaredClasses = get_declared_classes();
        array_walk($declaredClasses, function (&$class) {
            $class = strtolower($class);
        });
        static::$existingClasses = array_combine($declaredClasses, array_fill(0, count($declaredClasses), true));
    }
} 