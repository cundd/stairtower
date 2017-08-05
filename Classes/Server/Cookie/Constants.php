<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Server\Cookie;

/**
 * Constants for Cookies
 */
interface Constants
{
    /**
     * Header name to retrieve the sent cookies
     */
    const GET_COOKIE_HEADER_NAME = 'Cookie';

    /**
     * Header name to set cookies
     */
    const SET_COOKIE_HEADER_NAME = 'Set-Cookie';
}