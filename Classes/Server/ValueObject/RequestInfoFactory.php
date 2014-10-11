<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 10.10.14
 * Time: 14:59
 */

namespace Cundd\PersistentObjectStore\Server\ValueObject;
use React\Http\Request;

/**
 * Factory for RequestInfo instances
 *
 * @package Cundd\PersistentObjectStore\Server\ValueObject
 */
class RequestInfoFactory {
	/**
	 * Map of paths to their RequestInfo objects
	 *
	 * @var array
	 */
	static protected $pathToRequestInfoMap = array();

	/**
	 * Builds a RequestInfo instance for the given path
	 *
	 * @param Request $request
	 * @return RequestInfo
	 */
	static public function buildRequestInfoFromRequest(Request $request) {
		$requestInfoIdentifier = sprintf('%s-%s', $request->getMethod(), $request->getPath());
		if (!isset(static::$pathToRequestInfoMap[$requestInfoIdentifier])) {
			$pathParts = explode('/', $request->getPath());
			$pathParts = array_values(array_filter($pathParts, function ($item) {
				return !!$item;
			}));
			$dataIdentifier = NULL;
			$databaseIdentifier = NULL;
//			$format = 'json';
//			if (count($pathParts) >= 2) {
//				$dataIdentifier = $pathParts[1];
//			}
			if (count($pathParts) >= 2) {
				$dataIdentifier = $pathParts[1];
			}
			if (count($pathParts) >= 1) {
				$databaseIdentifier = $pathParts[0];
			}
			static::$pathToRequestInfoMap[$requestInfoIdentifier] = new RequestInfo($dataIdentifier, $databaseIdentifier, $request->getMethod());
		}
		return static::$pathToRequestInfoMap[$requestInfoIdentifier];
	}
} 