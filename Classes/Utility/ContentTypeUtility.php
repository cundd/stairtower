<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 11.10.14
 * Time: 13:06
 */

namespace Cundd\PersistentObjectStore\Utility;
use Cundd\PersistentObjectStore\Server\ContentType;

/**
 * Helper class to convert content types to file endings and vice versa
 *
 * @package Cundd\PersistentObjectStore\Server
 */
class ContentTypeUtility {
	/**
	 * Map of suffix to the content type
	 *
	 * @var array
	 */
	static protected $suffixToContentTypeMap = array(
		'jpg' => ContentType::JPEG_IMAGE,
		'jpeg' => ContentType::JPEG_IMAGE,
		'png' => ContentType::PNG_IMAGE,
		'gif' => ContentType::GIF_IMAGE,

		'json' => ContentType::JSON_APPLICATION,
		'js' => ContentType::JAVASCRIPT_APPLICATION,
		'xml' => ContentType::XML_TEXT,
		'html' => ContentType::HTML_TEXT,
		'plain' => ContentType::PLAIN_TEXT,
		'csv' => ContentType::CSV_TEXT,
		'css' => ContentType::CSS_TEXT,
	);

	/**
	 * Converts the content type to a file suffix
	 *
	 * @param string $contentType
	 * @return string
	 */
	static public function convertContentTypeToSuffix($contentType) {
		return array_search($contentType, static::$suffixToContentTypeMap);
	}

	/**
	 * Converts the file suffix to the content type
	 *
	 * @param string $suffix
	 * @return string
	 */
	static public function convertSuffixToContentType($suffix) {
		return isset(static::$suffixToContentTypeMap[$suffix]) ? static::$suffixToContentTypeMap[$suffix] : FALSE;
	}
} 