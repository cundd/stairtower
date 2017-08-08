<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Utility;

use Cundd\Stairtower\Server\ContentType;

/**
 * Helper class to convert content types to file endings and vice versa
 */
class ContentTypeUtility
{
    /**
     * Map of suffix to the content type
     *
     * @var array
     */
    protected static $suffixToContentTypeMap = [
        'jpg'   => ContentType::JPEG_IMAGE,
        'jpeg'  => ContentType::JPEG_IMAGE,
        'png'   => ContentType::PNG_IMAGE,
        'gif'   => ContentType::GIF_IMAGE,
        'json'  => ContentType::JSON_APPLICATION,
        'js'    => ContentType::JAVASCRIPT_APPLICATION,
        'xml'   => ContentType::XML_TEXT,
        'html'  => ContentType::HTML_TEXT,
        'plain' => ContentType::PLAIN_TEXT,
        'csv'   => ContentType::CSV_TEXT,
        'css'   => ContentType::CSS_TEXT,
    ];

    /**
     * Converts the content type to a file suffix
     *
     * @param string $contentType
     * @return string|false
     */
    public static function convertContentTypeToSuffix(string $contentType)
    {
        $result = array_search($contentType, static::$suffixToContentTypeMap);

        return false === $result ? false : (string)$result;
    }

    /**
     * Converts the file suffix to the content type
     *
     * @param string $suffix
     * @return string|false
     */
    public static function convertSuffixToContentType(string $suffix)
    {
        return isset(static::$suffixToContentTypeMap[$suffix]) ? static::$suffixToContentTypeMap[$suffix] : false;
    }
} 