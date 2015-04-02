<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 11.10.14
 * Time: 14:28
 */

namespace Cundd\PersistentObjectStore\Server\ValueObject;

use Cundd\PersistentObjectStore\Server\Controller\ControllerResultInterface;

/**
 * Controller result implementation
 *
 * @package Cundd\PersistentObjectStore\Server\ValueObject
 */
class ControllerResult extends HandlerResult implements ControllerResultInterface
{
    /**
     * Content type of the request
     *
     * @var string
     */
    protected $contentType;

    /**
     * Headers to send with the response
     *
     * @var array
     */
    protected $headers = array();

    /**
     * Creates a new result with the given data and status
     *
     * @param integer $statusCode
     * @param mixed   $data
     * @param string  $contentType
     * @param array   $headers
     */
    public function __construct($statusCode, $data = null, $contentType = null, $headers = array())
    {
        $this->statusCode  = $statusCode;
        $this->data        = $data;
        $this->contentType = $contentType;
        $this->headers     = (array)$headers;
    }

    /**
     * Returns the content type of the request
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType ? $this->contentType . '; charset=utf-8' : '';
    }

    /**
     * Retrieves all message headers.
     *
     * The keys represent the header name as it will be sent over the wire, and
     * each value is an array of strings associated with the header.
     *
     *     // Represent the headers as a string
     *     foreach ($message->getHeaders() as $name => $values) {
     *         echo $name . ": " . implode(", ", $values);
     *     }
     *
     *     // Emit headers iteratively:
     *     foreach ($message->getHeaders() as $name => $values) {
     *         foreach ($values as $value) {
     *             header(sprintf('%s: %s', $name, $value), false);
     *         }
     *     }
     *
     * While header names are not case-sensitive, getHeaders() will preserve the
     * exact case in which headers were originally specified.
     *
     * @return array Returns an associative array of the message's headers. Each
     *     key MUST be a header name, and each value MUST be an array of strings.
     */
    public function getHeaders()
    {
        $contentType = $this->getContentType();
        if (!$contentType) {
            return $this->headers;
        }
        return array_replace(
            $this->headers,
            [
                'Content-Type' => $this->getContentType(),
            ]
        );
    }
}
