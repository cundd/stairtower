<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Server\ValueObject;


interface RequestInterface
{
    /**
     * Returns the request body
     *
     * @return mixed
     */
    public function getBody();

    /**
     * Returns the identifier for the Document instance
     *
     * @return string
     */
    public function getDataIdentifier();

    /**
     * Return the identifier for the database
     *
     * @return string
     */
    public function getDatabaseIdentifier();

    /**
     * Returns the request method
     *
     * @return string
     */
    public function getMethod();

    /**
     * Returns the controller or special handler action
     *
     * @return string
     */
    public function getAction();

    /**
     * Returns the name part of the action
     *
     * @return string
     */
    public function getActionName();

    /**
     * Returns the special controller class name
     *
     * @return string
     */
    public function getControllerClass();

    /**
     * Returns if the request is a write request
     *
     * @return bool
     */
    public function isWriteRequest();

    /**
     * Returns if the request is a read request
     *
     * @return bool
     */
    public function isReadRequest();

    /**
     * Returns the headers
     *
     * @return array
     */
    public function getHeaders();

    /**
     * Returns the header value for the given name
     *
     * @param string $name
     * @return mixed
     */
    public function getHeader($name);

    /**
     * Returns the cookies
     *
     * @return mixed
     */
    public function getCookies();

    /**
     * Returns the cookie value for the given name
     *
     * @param string $name
     * @return mixed
     */
    public function getCookie($name);

    /**
     * Returns the requested content type
     *
     * @return string
     */
    public function getContentType();

    /**
     * Returns the path
     *
     * @return string
     */
    public function getPath();

    /**
     * Returns the query
     *
     * @return array
     */
    public function getQuery();

    /**
     * Returns the HTTP version
     *
     * @return string
     */
    public function getHttpVersion();

}