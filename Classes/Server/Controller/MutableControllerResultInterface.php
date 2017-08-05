<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 10.10.14
 * Time: 17:59
 */
namespace Cundd\PersistentObjectStore\Server\Controller;


/**
 * Interface for classes that describe a mutable Controller response
 *
 * @package Cundd\PersistentObjectStore\Server\Handler
 */
interface MutableControllerResultInterface extends ControllerResultInterface
{
    /**
     * Sets the content type of the request
     *
     * @param string $contentType
     * @return $this
     */
    public function setContentType($contentType);

    /**
     * Sets the headers to send with the response
     *
     * @param array $headers
     * @return $this
     */
    public function setHeaders($headers);

    /**
     * Add the header with the given name
     *
     * @param string $name
     * @param mixed  $header
     * @return $this
     */
    public function addHeader($name, $header);

    /**
     * Replace the header with the given name
     *
     * @param string $name
     * @param mixed  $header
     * @return $this
     */
    public function replaceHeader($name, $header);

    /**
     * Sets the status code for the response
     *
     * @param int $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode);

    /**
     * Sets the request's response data
     *
     * @param mixed $data
     * @return $this
     */
    public function setData($data);
}
