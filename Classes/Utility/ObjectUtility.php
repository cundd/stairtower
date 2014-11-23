<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 19.03.14
 * Time: 18:06
 */

namespace Cundd\PersistentObjectStore\Utility;

use Cundd\PersistentObjectStore\LogicException;

/**
 * Utility class for accessing object properties
 *
 * @package Cundd\Noshi\Utilities
 */
class ObjectUtility
{
    /**
     * Returns the value for the key path of the given object
     *
     * @param mixed        $value   New value to set
     * @param string       $keyPath Key path of the property to fetch
     * @param object|array $object  Source to fetch the data from
     * @throws LogicException if the given key path is no string
     */
    public static function setValueForKeyPathOfObject($value, $keyPath, $object)
    {
        if (!is_string($keyPath)) {
            throw new LogicException('Given key path is not of type string (maybe arguments are ordered incorrect)',
                1395484136);
        }

        $keyPathParts = explode('.', $keyPath);
        $key          = array_pop($keyPathParts);
        $currentValue = static::valueForKeyPathOfObject(implode('.', $keyPathParts), $object);

        $accessorMethod = 'set' . ucfirst($key);

        // Current value is an array
        if (is_array($currentValue)) {
            throw new LogicException('Setting nested array values is currently not supported', 1411209195);
            //$currentValue[$key] = $value;
        }

        // Current value is an object
        if (is_object($currentValue)) {
            if (method_exists($currentValue, $accessorMethod)) { // Getter method
                $currentValue->$accessorMethod($value);
            } else {
                if (method_exists($currentValue, 'set')) { // General "set" method
                    $currentValue->set($key);
                } else {
                    if (array_key_exists($key, get_object_vars($currentValue))) { // Direct access
                        $currentValue->$key = $value;
                    }
                }
            }
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
            throw new LogicException('Given key path is not of type string (maybe arguments are ordered incorrect)',
                1395484136);
        }

        $currentValue = $object;
//		$keyPathParts = explode('.', $keyPath);
//		$key          = current($keyPathParts);

        $key = strtok($keyPath, '.');
        do {
            // Current value is an array
            if (is_array($currentValue) && isset($currentValue[$key])) {
                $currentValue = $currentValue[$key];
            } else // Current value is an object
            {
                if (is_object($currentValue)) {
                    $accessorMethod = 'get' . ucfirst($key);

                    if (method_exists($currentValue, $accessorMethod)) { // Getter method
                        $currentValue = $currentValue->$accessorMethod();
                    } else {
                        if (method_exists($currentValue, 'get')) { // General "get" method
                            $currentValue = $currentValue->get($key);
                        } else {
                            if (array_key_exists($key, get_object_vars($currentValue))) { // Direct access
                                $currentValue = $currentValue->$key;
                            } else {
                                $currentValue = null;
                            }
                        }
                    }
                } else {
                    $currentValue = null;
                }
            }

            if ($currentValue === null) {
                break;
            }

        } while ($key = strtok('.'));

        if (strtok('.') && func_num_args() > 2) {
            if (is_object($default) && ($default instanceof \Closure)) {
                $currentValue = $default();
            } else {
                $currentValue = $default;
            }
        }


//		$keyPathParts = explode('.', $keyPath);
//		$key          = current($keyPathParts);
//		do {
//			// Current value is an array
//			if (is_array($currentValue) && isset($currentValue[$key])) {
//				$currentValue = $currentValue[$key];
//			} else
//
//			// Current value is an object
//			if (is_object($currentValue)) {
//				$accessorMethod = 'get' . ucfirst($key);
//
//				if (method_exists($currentValue, $accessorMethod)) { // Getter method
//					$currentValue = $currentValue->$accessorMethod();
//				} else if (method_exists($currentValue, 'get')) { // General "get" method
//					$currentValue = $currentValue->get($key);
//				} else if (array_key_exists($key, get_object_vars($currentValue))) { // Direct access
//					$currentValue = $currentValue->$key;
//				} else {
//					$currentValue = NULL;
//				}
//			} else {
//				$currentValue = NULL;
//			}
//
//			if ($currentValue === NULL) break;
//
//		} while ($key = next($keyPathParts));
//
//		if (next($keyPathParts) && func_num_args() > 2) {
//			if (is_object($default) && ($default instanceof \Closure)) {
//				$currentValue = $default();
//			} else {
//				$currentValue = $default;
//			}
//		}
        return $currentValue;
    }
} 