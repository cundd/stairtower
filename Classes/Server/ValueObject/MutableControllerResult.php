<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Server\ValueObject;

use Cundd\PersistentObjectStore\Server\ContentType;
use Cundd\PersistentObjectStore\Server\Controller\MutableControllerResultInterface;

/**
 * Controller result implementation
 */
class MutableControllerResult extends AbstractControllerResult implements MutableControllerResultInterface
{
    /**
     * Creates a new result with the given data and status
     *
     * @param integer $statusCode
     * @param mixed   $data
     * @param string  $contentType
     * @param array   $headers
     */
    public function __construct(int $statusCode = null, $data = null, string $contentType = ContentType::HTML_TEXT, array $headers = [])
    {
        parent::__construct($statusCode, $data, $contentType, $headers);
    }

    public function setContentType(string $contentType): MutableControllerResultInterface
    {
        $this->contentType = $contentType;

        return $this;
    }

    public function setHeaders(array $headers): MutableControllerResultInterface
    {
        $this->headers = $headers;

        return $this;
    }

    public function addHeader(string $name, $header): MutableControllerResultInterface
    {
        if (isset($this->headers[$name])) {
            $existingHeader = $this->headers[$name];
            if (is_array($existingHeader)) {
                $this->headers[$name][] = $header;
            } else {
                $this->headers[$name] = [$existingHeader, $header];
            }
        } else {
            $this->headers[$name] = $header;
        }

        return $this;
    }

    public function replaceHeader(string $name, $header): MutableControllerResultInterface
    {
        $this->headers[$name] = $header;

        return $this;
    }

    public function setStatusCode(int $statusCode): MutableControllerResultInterface
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public function setData($data): MutableControllerResultInterface
    {
        $this->data = $data;

        return $this;
    }
}
