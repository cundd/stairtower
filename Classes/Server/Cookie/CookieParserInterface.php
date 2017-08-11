<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server\Cookie;

use Cundd\Stairtower\Server\ValueObject\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface for classes that can parse and transform request cookies
 */
interface CookieParserInterface
{
    /**
     * Parse the cookie data from the given request and transform it into objects
     *
     * @param RequestInterface|ServerRequestInterface $request
     * @return Cookie[] Returns a dictionary with the cookie names as keys
     */
    public function parse($request);
} 