<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server\ValueObject;


use Cundd\Stairtower\Server\Cookie\Cookie;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

interface RequestInterface
{
    /**
     * Returns the request body as stream
     *
     * @return StreamInterface|null
     */
    public function getBody(): ?StreamInterface;

    /**
     * Returns the parsed request body
     *
     * @return mixed
     */
    public function getParsedBody();

    /**
     * Returns a copy of the Request with the given prepared body
     *
     * @param $parsedBody
     * @return RequestInterface
     */
    public function withParsedBody($parsedBody): RequestInterface;

    /**
     * Returns the identifier for the Document instance
     *
     * @return string
     */
    public function getDataIdentifier(): string;

    /**
     * Return the identifier for the database
     *
     * @return string
     */
    public function getDatabaseIdentifier(): string;

    /**
     * Returns the request method
     *
     * @return string
     */
    public function getMethod(): string;

    /**
     * Returns the controller or special handler action
     *
     * @return string
     */
    public function getAction(): string;

    /**
     * Returns the name part of the action
     *
     * @return string
     */
    public function getActionName(): string;

    /**
     * Returns the special controller class name
     *
     * @return string
     */
    public function getControllerClass(): string;

    /**
     * Returns if the request is a write request
     *
     * @return bool
     */
    public function isWriteRequest(): bool;

    /**
     * Returns if the request is a read request
     *
     * @return bool
     */
    public function isReadRequest(): bool;

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
     * @return array
     */
    public function getHeader(string $name): array;

    /**
     * Returns the cookies
     *
     * @return Cookie[]
     */
    public function getCookies(): array;

    /**
     * Returns the cookie value for the given name
     *
     * @param string $name
     * @return mixed
     */
    public function getCookie(string $name): ?Cookie;

    /**
     * Returns the requested content type
     *
     * @return string
     */
    public function getContentType(): string;

    /**
     * Returns the path
     *
     * @return string
     */
    public function getPath(): string;

    /**
     * Returns the query parameters
     *
     * @return array
     */
    public function getQuery(): array;

    /**
     * Returns the HTTP version
     *
     * @return string
     */
    public function getHttpVersion(): string;

    /**
     * Returns the requested URI
     *
     * @return UriInterface
     */
    public function getUri(): UriInterface;

}