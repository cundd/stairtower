<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 13.10.14
 * Time: 17:12
 */

namespace Cundd\PersistentObjectStore\Server\BodyParser;

use React\Http\Request;

/**
 * Interface for classes that can parse a given request body
 *
 * @package Cundd\PersistentObjectStore\Server\BodyParser
 */
interface BodyParserInterface
{
    /**
     * @param string  $data
     * @param Request $request
     * @return mixed
     */
    public function parse($data, $request);
}
