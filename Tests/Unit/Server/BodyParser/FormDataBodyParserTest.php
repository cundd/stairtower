<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 13.10.14
 * Time: 17:17
 */

namespace Cundd\PersistentObjectStore\Server\BodyParser;


use Cundd\PersistentObjectStore\AbstractCase;
use Cundd\PersistentObjectStore\Server\ValueObject\RequestInterface;

class FormDataBodyParserTest_DummyRequestClass implements RequestInterface
{
    public function getPath()
    {
        return '/contacts/';
    }

    public function getMethod()
    {
        return 'GET';
    }

    /**
     * Returns the request body
     *
     * @return mixed
     */
    public function getBody()
    {
    }

    /**
     * Returns the identifier for the Document instance
     *
     * @return string
     */
    public function getDataIdentifier()
    {
    }

    /**
     * Return the identifier for the database
     *
     * @return string
     */
    public function getDatabaseIdentifier()
    {
    }

    /**
     * Returns the controller or special handler action
     *
     * @return string
     */
    public function getAction()
    {
    }

    /**
     * Returns the name part of the action
     *
     * @return string
     */
    public function getActionName()
    {
    }

    /**
     * Returns the special controller class name
     *
     * @return string
     */
    public function getControllerClass()
    {
    }

    /**
     * Returns if the request is a write request
     *
     * @return bool
     */
    public function isWriteRequest()
    {
    }

    /**
     * Returns if the request is a read request
     *
     * @return bool
     */
    public function isReadRequest()
    {
    }

    /**
     * Returns the headers
     *
     * @return array
     */
    public function getHeaders()
    {
    }

    /**
     * Returns the header value for the given name
     *
     * @param string $name
     * @return mixed
     */
    public function getHeader($name)
    {
    }

    /**
     * Returns the cookies
     *
     * @return mixed
     */
    public function getCookies()
    {
    }

    /**
     * Returns the cookie value for the given name
     *
     * @param string $name
     * @return mixed
     */
    public function getCookie($name)
    {
    }

    /**
     * Returns the requested content type
     *
     * @return string
     */
    public function getContentType()
    {
    }

    /**
     * Returns the query
     *
     * @return array
     */
    public function getQuery()
    {
    }

    /**
     * Returns the HTTP version
     *
     * @return string
     */
    public function getHttpVersion()
    {
    }
}

/**
 * @package Cundd\PersistentObjectStore\Server\BodyParser
 */
class FormDataBodyParserTest extends AbstractCase
{
    /**
     * @var BodyParserInterface
     */
    protected $fixture;

    /**
     * @test
     */
    public function parseBodyTest()
    {
        /** @var RequestInterface $dummyRequest */
        $dummyRequest = new FormDataBodyParserTest_DummyRequestClass();

        $this->assertArrayHasKey('email', $this->fixture->parse('email=test%40cundd.net&name=Daniel', $dummyRequest));
        $this->assertArrayHasKey('email', $this->fixture->parse('name=Daniel&email=test%40cundd.net', $dummyRequest));
        $this->assertArrayHasKey('email', $this->fixture->parse('email=test%40cundd.net', $dummyRequest));
    }
}
 