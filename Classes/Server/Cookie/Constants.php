<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 06.04.15
 * Time: 17:53
 */

namespace Cundd\PersistentObjectStore\Server\Cookie;

/**
 * Constants for Cookies
 *
 * @package Cundd\PersistentObjectStore\Server\Cookie
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