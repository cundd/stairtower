<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 13.10.14
 * Time: 17:11
 */

namespace Cundd\PersistentObjectStore\Server\Cookie;

use Cundd\PersistentObjectStore\Exception\InvalidArgumentError;
use Cundd\PersistentObjectStore\Server\ValueObject\RequestInterface;
use Cundd\PersistentObjectStore\Utility\GeneralUtility;

/**
 * Implementation of cookie parsers
 *
 * @package Cundd\PersistentObjectStore\Server\BodyParser
 */
class CookieParser implements CookieParserInterface
{
    /**
     * @var \Guzzle\Parser\Cookie\CookieParser
     * @Inject
     */
    protected $concreteCookieParser;

    /**
     * Parse the cookie data from the given request and transform it into objects
     *
     * @param RequestInterface|\React\Http\Request $request
     * @return Cookie[] Returns a dictionary with the cookie names as keys
     * @throws InvalidArgumentError if the headers could not be read from the given request
     */
    public function parse($request)
    {
        if ($request instanceof RequestInterface) {
            $cookieString = $request->getHeader(Constants::GET_COOKIE_HEADER_NAME);
        } elseif (method_exists($request, 'getHeaders')) {
            $headers      = $request->getHeaders();
            $cookieString = isset($headers[Constants::GET_COOKIE_HEADER_NAME]) ? $headers[Constants::GET_COOKIE_HEADER_NAME] : '';
        } else {
            throw new InvalidArgumentError(
                sprintf('Could not retrieve cookie header from argument of type %s', GeneralUtility::getType($request)),
                1428081672
            );
        }

        $parsedCookies = $this->concreteCookieParser->parseCookie($cookieString);
        if (!$parsedCookies) {
            return [];
        }

        $cookieObjects = [];
        foreach ($parsedCookies['cookies'] as $cookieName => $cookieValue) {
            $cookieObjects[$cookieName] = new Cookie(
                $cookieName,
                $cookieValue,
                $parsedCookies['expires'],
                $parsedCookies['path'],
                $parsedCookies['domain'],
                $parsedCookies['secure'],
                $parsedCookies['http_only']
            );
        }

        return $cookieObjects;
    }
}
