<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 10.10.14
 * Time: 14:59
 */

namespace Cundd\PersistentObjectStore\Server\ValueObject;

/**
 * Factory for PathInfo instances
 *
 * @package Cundd\PersistentObjectStore\Server\ValueObject
 */
class PathInfoFactory {
	/**
	 * Map of paths to their PathInfo objects
	 *
	 * @var array
	 */
	static protected $pathToPathInfoMap = array();

	/**
	 * Builds a PathInfo instance for the given path
	 *
	 * @param string $path
	 * @return PathInfo
	 */
	static public function buildPathInfoFromPath($path) {
		if (!isset(static::$pathToPathInfoMap[$path])) {
			$pathParts = explode('/', $path);
			$pathParts = array_values(array_filter($pathParts, function ($item) {
				return !!$item;
			}));
			$dataIdentifier = NULL;
			$databaseIdentifier = NULL;
			if (count($pathParts) >= 2) {
				$dataIdentifier = $pathParts[1];
			}
			if (count($pathParts) >= 1) {
				$databaseIdentifier = $pathParts[0];
			}
			static::$pathToPathInfoMap[$path] = new PathInfo($dataIdentifier, $databaseIdentifier);
		}
		return static::$pathToPathInfoMap[$path];
	}
} 