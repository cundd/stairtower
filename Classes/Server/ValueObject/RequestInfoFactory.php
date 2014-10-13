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
			$handlerAction = static::getHandlerActionForRequest($request);
			if ($handlerAction) {
				$databaseIdentifier = '';
			}
			static::$pathToRequestInfoMap[$requestInfoIdentifier] = new RequestInfo($dataIdentifier, $databaseIdentifier, $request->getMethod(), $handlerAction);
		}
		return static::$pathToRequestInfoMap[$requestInfoIdentifier];
	}

	/**
	 * Returns the handler action if the path contains a special information identifier, otherwise FALSE
	 *
	 * @param Request $request
	 * @return string|bool
	 */
	static public function getHandlerActionForRequest($request) {
		return static::getActionForRequestAndInterface($request, 'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface');

	}

	/**
	 * Returns the special server action if the path contains a special information identifier, otherwise FALSE
	 *
	 * @param Request $request
	 * @return string|bool
	 */
	static public function getServerActionForRequest($request) {
		$path = $request->getPath();
		$method = $request->getMethod();
		if ($path[0] === '/') {
			$path = substr($path, 1);
		}
		return substr($path, 0, 8) === '_restart' && $method === 'POST' ? 'restart' : FALSE;
	}


	/**
	 * Returns an action method name if the path contains a special information identifier, otherwise FALSE
	 *
	 * @param Request $request
	 * @param string $interface
	 * @return string|bool
	 */
	static protected function getActionForRequestAndInterface($request, $interface) {
		$path = $request->getPath();
		$method = $request->getMethod();
		if ($path[0] === '/') {
			$path = substr($path, 1);
		}
		if ($path[0] === '_') {
			list($path,) = explode('/', $path, 2);
			$handlerAction = strtolower($method) . ucfirst(substr($path, 1)) . 'Action';
			if (method_exists($interface, $handlerAction)) {
				return $handlerAction;
			}
		}
		return FALSE;
	}
} 