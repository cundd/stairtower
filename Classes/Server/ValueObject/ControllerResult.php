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
     * Creates a new result with the given data and status
     *
     * @param integer $statusCode
     * @param mixed   $data
     * @param string  $contentType
     */
    public function __construct($statusCode, $data = null, $contentType = null)
    {
        $this->statusCode  = $statusCode;
        $this->data        = $data;
        $this->contentType = $contentType;
    }

    /**
     * Returns the content type of the request
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }


}