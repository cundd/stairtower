<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 02.04.15
 * Time: 21:31
 */

namespace Cundd\PersistentObjectStore\Server\ValueObject;


use Cundd\PersistentObjectStore\Exception\InvalidArgumentError;
use Cundd\PersistentObjectStore\Immutable;
use DateTime;

class Cookie implements Immutable
{
//Set-Cookie: lu=Rg3vHJZnehYLjVg7qi3bZjzg; Expires=Tue, 15-Jan-2013 21:47:38 GMT; Path=/; Domain=.example.com; HttpOnly
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