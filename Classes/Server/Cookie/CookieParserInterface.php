<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 13.10.14
 * Time: 17:12
 */

namespace Cundd\PersistentObjectStore\Server\Cookie;

use Cundd\PersistentObjectStore\Server\ValueObject\RequestInterface;

/**
 * Interface for classes that can parse and transform request cookies
 *
 * @package Cundd\PersistentObjectStore\Server\BodyParser
 */
interface CookieParserInterface
{
    /**
     * Parse the cookie data from the given request and transform it into objects
     *
     * @param RequestInterface|\React\Http\Request $request
     * @return Cookie[] Returns a dictionary with the cookie names as keys
     */
    public function parse($request);
} 