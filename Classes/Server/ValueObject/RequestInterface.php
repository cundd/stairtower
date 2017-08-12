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
     * Retrieve any parameters provided in the request body.
     *
     * If the request Content-Type is either application/x-www-form-urlencoded
     * or multipart/form-data, and the request method is POST, this method MUST
     * return the contents of $_POST.
     *
     * Otherwise, this method may return any results of deserializing
     * the request body content; as parsing returns structured content, the
     * potential types MUST be arrays or objects only. A null value indicates
     * the absence of body content.
     *
     * @return null|array|object The deserialized body parameters, if any.
     *     These will typically be an array or object.
     */
    public function getParsedBody();

    /**
     * Return an instance with the specified body parameters.
     *
     * These MAY be injected during instantiation.
     *
     * If the request Content-Type is either application/x-www-form-urlencoded
     * or multipart/form-data, and the request method is POST, use this method
     * ONLY to inject the contents of $_POST.
     *
     * The data IS NOT REQUIRED to come from $_POST, but MUST be the results of
     * deserializing the request body content. Deserialization/parsing returns
     * structured data, and, as such, this method ONLY accepts arrays or objects,
     * or a null value if nothing was available to parse.
     *
     * As an example, if content negotiation determines that the request data
     * is a JSON payload, this method could be used to create a request
     * instance with the deserialized parameters.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated body parameters.
     *
     * @param null|array|object $data The deserialized body data. This will
     *     typically be in an array or object.
     * @return static
     * @throws \InvalidArgumentException if an unsupported argument type is
     *     provided.
     */
    public function withParsedBody($data): RequestInterface;

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