<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 25.08.14
 * Time: 21:40
 */

namespace Cundd\PersistentObjectStore\Utility;

use Cundd\PersistentObjectStore\Configuration\ConfigurationManager;
use Cundd\PersistentObjectStore\Domain\Model\Exception\InvalidDatabaseIdentifierException;
use Cundd\PersistentObjectStore\Domain\Model\Exception\InvalidDataIdentifierException;
use Cundd\PersistentObjectStore\Server\Exception\InvalidRequestMethodException;

/**
 * Interface GeneralUtilityInterface
 *
 * @package Cundd\PersistentObjectStore\Utility
 */
abstract class GeneralUtility
{
    /**
     * Checks if the given database identifier is valid
     *
     * @param string $identifier
     * @throws \Cundd\PersistentObjectStore\Domain\Model\Exception\InvalidDatabaseIdentifierException if the database isn't valid
     */
    public static function assertDatabaseIdentifier($identifier)
    {
        if (!preg_match('(^([a-zA-Z]{1}[a-zA-Z0-9_\-]{0,})$)', $identifier)) {
            throw new InvalidDatabaseIdentifierException("Invalid database identifier '$identifier'", 1408996075);
        }
    }

    /**
     * Checks if the given data identifier is valid
     *
     * @param string $identifier
     * @throws \Cundd\PersistentObjectStore\Domain\Model\Exception\InvalidDataIdentifierException if the database isn't valid
     */
    public static function assertDataIdentifier($identifier)
    {
        if (!preg_match('(^([a-zA-Z0-9]{1}[a-zA-Z0-9_\-\.@]{0,})$)', $identifier)) {
            throw new InvalidDataIdentifierException("Invalid data identifier '$identifier'", 1412889537);
        }
    }

    /**
     * Checks if the given method is a valid HTTP method
     *
     * @param string $method
     * @throw \Cundd\PersistentObjectStore\Server\Exception\InvalidRequestMethodServerException
     */
    public static function assertRequestMethod($method)
    {
        if (!in_array($method, array('GET', 'POST', 'PUT', 'DELETE', 'HEAD'))) {
            throw new InvalidRequestMethodException("Invalid method '$method'", 1413052000);
        }
    }

    /**
     * Format the given memory amount
     *
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    public static function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow   = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow   = min($pow, count($units) - 1);

        // Uncomment one of the following alternatives
        $bytes /= pow(1024, $pow);
        // $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Returns the integer value of the given variable or NULL if it is no valid integer
     *
     * @param $var
     * @return int|null
     */
    public static function validateInteger($var)
    {
        if (is_integer($var) || ((string)(int)$var === $var)) {
            return intval($var);
        }
        return null;
    }

    /**
     * Transform the underscored_string to camelCase
     *
     * @param string $underscoreString
     * @return string
     */
    public static function underscoreToCamelCase($underscoreString)
    {
        $prefix = $underscoreString[0] === '_' ? '_' : '';
        return $prefix . lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $underscoreString))));
    }

    /**
     * Tries to convert the given value to a string
     *
     * @param mixed $value
     * @return bool|string Returns the string representation or FALSE on error
     */
    public static function toString($value)
    {
        switch (true) {
            case is_null($value):
                return '';

            case is_scalar($value):
                return (string)$value;

            case is_resource($value):
                return (string)$value;

            case is_array($value):
                return implode(' ', $value);

            case is_object($value) && method_exists($value, '__toString'):
                return (string)$value;
        }
        return false;
    }

    /**
     * Removes the directory recursive
     *
     * @param string $dir
     * @return bool
     */
    public static function removeDirectoryRecursive($dir)
    {
        $success = true;
        $files   = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            if (is_dir("$dir/$file")) {
                $success *= self::removeDirectoryRecursive("$dir/$file");
            } else {
                $success *= unlink("$dir/$file");
            }
        }
        return (bool)($success * rmdir($dir));
    }

    /**
     * Create the directory (recursive)
     *
     * @param string $dir
     * @param bool   $recursive
     * @return bool
     */
    public static function createDirectoryRecursive($dir, $recursive = false)
    {
        return mkdir($dir,
            octdec(ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('fs.folderMode')), $recursive);
    }
} 