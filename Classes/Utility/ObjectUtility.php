<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 19.03.14
 * Time: 18:06
 */

namespace Cundd\PersistentObjectStore\Utility;

use Cundd\PersistentObjectStore\KeyValueCodingInterface;
use Cundd\PersistentObjectStore\LogicException;

/**
 * Utility class for accessing object properties
 *
 * @package Cundd\PersistentObjectStore\Utilities
 */
class ObjectUtility
{
    /**
     * Returns the value for the key path of the given object
     *
     * @param mixed        $value    New value to set
     * @param string       $keyPath  Key path of the property to fetch
     * @param object|array $object   Source to fetch the data from
     * @param bool         $graceful Defines if an exception should be thrown if no setter could be found to set the value
     * @throws LogicException if the given key path is no string
     */
    public static function setValueForKeyPathOfObject($value, $keyPath, $object, $graceful = false)
    {
        if (!is_string($keyPath)) {
            throw new LogicException('Given key path is not of type string (maybe arguments are ordered incorrect)',
                1395484136);
        }
        if ($keyPath === '') {
            throw new LogicException('Given key path is empty', 1395484137);
        }

        $successfullySet = true;
        $keyPathParts    = explode('.', $keyPath);
        $key             = array_pop($keyPathParts);
        if (count($keyPathParts) > 0) {
            $currentValue = static::valueForKeyPathOfObject(implode('.', $keyPathParts), $object);
        } else {
            $currentValue = $object;
        }

        $accessorMethod = 'set' . ucfirst($key);

        // Current value is an array
        if (is_array($currentValue)) {
            throw new LogicException('Setting nested array values is currently not supported', 1411209195);
            //$currentValue[$key] = $value;
        }

        // Current value is an object
        if (is_object($currentValue)) {
            if ($currentValue instanceof KeyValueCodingInterface) { // Key value coding
                $currentValue->setValueForKey($value, $key);
            } elseif (method_exists($currentValue, $accessorMethod)) { // Setter method
                $currentValue->$accessorMethod($value);
            } elseif (method_exists($currentValue, 'set')) { // General "set" method
                $currentValue->set($key);
            } elseif (array_key_exists($key, get_object_vars($currentValue))) { // Direct access
                $currentValue->$key = $value;
            } else {
                $successfullySet = false;
            }
        } else {
            $successfullySet = false;
        }

        if (!$successfullySet && !$graceful) {
            throw new LogicException(
                sprintf(
                    'Could not set value for keyPath \'%s\' for object of type %s',
                    $keyPath,
                    is_object($object) ? get_class($object) : gettype($object)
                ),
                1411209195,
                new LogicException(
                    sprintf(
                        'Could not set value for current value of type %s with key \'%s\'',
                        is_object($currentValue) ? get_class($currentValue) : gettype($currentValue),
                        $key
                    )
                )
            );
        }
    }

    /**
     * Returns the value for the key path of the given object
     *
     * @param string       $keyPath Key path of the property to fetch
     * @param object|array $object  Source to fetch the data from
     * @param mixed        $default An optional default value to return if the path could not be resolved. If a callback is passed, it's return value is used
     * @throws LogicException if the given key path is no string
     * @return mixed
     */
    public static function valueForKeyPathOfObject($keyPath, $object, $default = null)
    {
        if (!is_string($keyPath)) {
            throw new LogicException(
                'Given key path is not of type string (maybe arguments are ordered incorrect)',
                1395484136
            );
        }
        if ($keyPath === '') {
            throw new LogicException('Given key path is empty', 1395484137);
        }

        $pathFullyResolved = true;
        $currentValue      = $object;
        $key               = strtok($keyPath, '.');
        do {
            // Current value is an array
            if (is_array($currentValue) && isset($currentValue[$key])) {
                $currentValue = $currentValue[$key];
            } elseif (is_object($currentValue)) { // Current value is an object
                $accessorMethod = 'get' . ucfirst($key);

                if ($currentValue instanceof KeyValueCodingInterface) { // Key value coding
                    $currentValue = $currentValue->valueForKey($key);
                } elseif ($currentValue instanceof \ArrayAccess) { // ArrayAccess
                    if ($currentValue->offsetExists($key)) {
                        $currentValue = $currentValue[$key];
                    } elseif (is_numeric($key) && $currentValue->offsetExists(intval($key))) {
                        $currentValue = $currentValue[intval($key)];
                    }
                } elseif (method_exists($currentValue, $accessorMethod)) { // Getter method
                    $currentValue = $currentValue->$accessorMethod();
                } elseif (method_exists($currentValue, 'get')) { // General "get" method
                    $currentValue = $currentValue->get($key);
                } elseif (array_key_exists($key, get_object_vars($currentValue))) { // Direct access
                    $currentValue = $currentValue->$key;
                } else {
                    $pathFullyResolved = false;
                    $currentValue      = null;
                }
            } else {
                $pathFullyResolved = false;
                $currentValue      = null;
            }

            if ($currentValue === null) {
                break;
            }

            $key = strtok('.');
        } while ($key !== false);

        if (($pathFullyResolved === false || strtok('.'))
            && func_num_args() > 2
        ) {
            if (is_object($default) && ($default instanceof \Closure)) {
                $currentValue = $default();
            } else {
                $currentValue = $default;
            }
        }
        return $currentValue;
    }
} 