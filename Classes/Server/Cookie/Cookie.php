<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 02.04.15
 * Time: 21:31
 */

namespace Cundd\PersistentObjectStore\Server\Cookie;


use Cundd\PersistentObjectStore\Exception\InvalidArgumentError;
use Cundd\PersistentObjectStore\Immutable;
use DateTime;

/**
 * Cookie representation
 *
 * @package Cundd\PersistentObjectStore\Server\Cookie
 */
class Cookie implements Immutable
{
    /**
     * Name of the cookie
     *
     * @var string
     */
    protected $name = '';

    /**
     * Value of the cookie
     *
     * @var string
     */
    protected $value;

    /**
     * Expiration date
     *
     * @var DateTime
     */
    protected $expires;

    /**
     * Path the cookie belongs to
     *
     * @link http://en.wikipedia.org/wiki/HTTP_cookie#Domain_and_Path
     * @var string
     */
    protected $path = '';

    /**
     * Domain the cookie belongs to
     *
     * @link http://en.wikipedia.org/wiki/HTTP_cookie#Domain_and_Path
     * @var string
     */
    protected $domain = '';

    /**
     * Set the cookie to HttpOnly
     *
     * @link http://en.wikipedia.org/wiki/HTTP_cookie#Secure_and_HttpOnly
     * @var bool
     */
    protected $httpOnly = false;

    /**
     * Set the cookie to Secure
     *
     * @link http://en.wikipedia.org/wiki/HTTP_cookie#Secure_and_HttpOnly
     * @var bool
     */
    protected $secure = false;

    /**
     * Defines if the value should be URL encoded
     *
     * @var bool
     */
    protected $urlEncode = true;

    function __construct(
        $name,
        $value,
        $expires = null,
        $path = null,
        $domain = null,
        $secure = false,
        $httpOnly = false,
        $urlEncode = true
    ) {
        if (!$name) {
            throw new InvalidArgumentError('Missing argument cookie "name"', 1428005989);
        }

        if ($expires instanceof DateTime) {
            $expires = clone $expires;
            $expires->setTimezone(new \DateTimeZone('GMT'));
        }

        $this->name      = $name;
        $this->value     = $value;
        $this->expires   = $expires;
        $this->path      = $path;
        $this->domain    = $domain;
        $this->secure    = (bool)$secure;
        $this->httpOnly  = (bool)$httpOnly;
        $this->urlEncode = (bool)$urlEncode;
    }

    /**
     * Name of the cookie
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Value of the cookie
     *
     * @return string
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Expiration date
     *
     * @return DateTime
     */
    public function getExpires() {
        return $this->expires;
    }

    /**
     * Path the cookie belongs to
     *
     * @link http://en.wikipedia.org/wiki/HTTP_cookie#Domain_and_Path
     * @return string
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * Domain the cookie belongs to
     *
     * @link http://en.wikipedia.org/wiki/HTTP_cookie#Domain_and_Path
     * @return string
     */
    public function getDomain() {
        return $this->domain;
    }

    /**
     * Set the cookie to HttpOnly
     *
     * @link http://en.wikipedia.org/wiki/HTTP_cookie#Secure_and_HttpOnly
     * @return bool
     */
    public function getHttpOnly() {
        return $this->httpOnly;
    }

    /**
     * Set the cookie to Secure
     *
     * @link http://en.wikipedia.org/wiki/HTTP_cookie#Secure_and_HttpOnly
     * @return bool
     */
    public function getSecure() {
        return $this->secure;
    }

    /**
     * Defines if the value should be URL encoded
     *
     * @return bool
     */
    public function getUrlEncode() {
        return $this->urlEncode;
    }

    /**
     * Transforms the cookie into a string that can be used as response header
     *
     * @return string
     */
    public function toHeader()
    {
        $definitions = [];

        if ($this->urlEncode) {
            $definitions[$this->name] = urlencode($this->value);
        } else {
            $definitions[$this->name] = $this->value;
        }

        if ($this->path !== null) {
            $definitions['Path'] = $this->path;
        }
        if ($this->expires !== null) {
            $definitions['Expires'] = $this->formatDate($this->expires);

        }
        if ($this->domain !== null) {
            $definitions['Domain'] = $this->domain;
        }

        $parts = [];
        foreach ($definitions as $name => $value) {
            $parts[] = $name . '=' . $value;
        }

        if ($this->secure) {
            $parts[] = 'Secure';
        }
        if ($this->httpOnly) {
            $parts[] = 'HttpOnly';
        }

        return implode('; ', $parts);
    }

    function __toString()
    {
        return $this->toHeader();
    }

    /**
     * Formats the given date according to the specification
     *
     * @link http://tools.ietf.org/html/rfc2616#section-3.3.1
     *
     * @param DateTime|string $date
     * @return string
     */
    protected function formatDate($date)
    {
        if ($date instanceof DateTime) {
            $dateDefinition = $date->format('D, d M Y H:i:s e');
            if (substr($dateDefinition, -4) === ' UTC') {
                return substr($dateDefinition, 0, -3) . 'GMT';
            } else {
                return $dateDefinition;
            }
        }
        return (string) $date;
    }
}
