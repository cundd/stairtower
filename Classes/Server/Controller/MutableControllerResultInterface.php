<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Server\Controller;


/**
 * Interface for classes that describe a mutable Controller response
 */
interface MutableControllerResultInterface extends ControllerResultInterface
{
    /**
     * Sets the content type of the request
     *
     * @param string $contentType
     * @return MutableControllerResultInterface
     */
    public function setContentType(string $contentType): MutableControllerResultInterface;

    /**
     * Sets the headers to send with the response
     *
     * @param array $headers
     * @return MutableControllerResultInterface
     */
    public function setHeaders(array $headers): MutableControllerResultInterface;

    /**
     * Add the header with the given name
     *
     * @param string $name
     * @param mixed  $header
     * @return MutableControllerResultInterface
     */
    public function addHeader(string $name, $header): MutableControllerResultInterface;

    /**
     * Replace the header with the given name
     *
     * @param string $name
     * @param mixed  $header
     * @return MutableControllerResultInterface
     */
    public function replaceHeader(string $name, $header): MutableControllerResultInterface;

    /**
     * Sets the status code for the response
     *
     * @param int $statusCode
     * @return MutableControllerResultInterface
     */
    public function setStatusCode(int $statusCode): MutableControllerResultInterface;

    /**
     * Sets the request's response data
     *
     * @param mixed $data
     * @return MutableControllerResultInterface
     */
    public function setData($data): MutableControllerResultInterface;
}
