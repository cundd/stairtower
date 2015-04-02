<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 13.10.14
 * Time: 17:11
 */

namespace Cundd\PersistentObjectStore\Server\BodyParser;

use Cundd\PersistentObjectStore\Server\ValueObject\RequestInterface;

/**
 * Body Parser implementation that can parse form data
 *
 * @package Cundd\PersistentObjectStore\Server\BodyParser
 */
class FormDataBodyParser implements BodyParserInterface
{
    /**
     * @param string  $data
     * @param RequestInterface $request
     * @return mixed
     */
    public function parse($data, RequestInterface $request)
    {
        $parsedData = array();
        parse_str($data, $parsedData);
        return $parsedData;
    }

} 