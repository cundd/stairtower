<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 13.10.14
 * Time: 17:12
 */

namespace Cundd\PersistentObjectStore\Server\BodyParser;

use Cundd\PersistentObjectStore\Server\ValueObject\RequestInterface;

/**
 * Interface for classes that can parse a given request body
 *
 * @package Cundd\PersistentObjectStore\Server\BodyParser
 */
interface BodyParserInterface
{
    /**
     * @param string  $data
     * @param RequestInterface $request
     * @return mixed
     */
    public function parse($data, RequestInterface $request);
} 