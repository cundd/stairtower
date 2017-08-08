<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server\ValueObject;

use Cundd\Stairtower\Server\ContentType;
use Cundd\Stairtower\Server\Controller\ControllerResultInterface;

/**
 * Abstract controller result implementation
 */
abstract class AbstractControllerResult extends AbstractHandlerResult implements ControllerResultInterface
{
    /**
     * Content type of the request
     *
     * @var string
     */
    public $contentType;

    /**
     * Headers to send with the response
     *
     * @var array
     */
    protected $headers = [];

    /**
     * Creates a new result with the given data and status
     *
     * @param integer $statusCode
     * @param mixed   $data
     * @param string  $contentType
     * @param array   $headers
     */
    public function __construct(
        int $statusCode,
        $data = null,
        ?string $contentType = ContentType::HTML_TEXT,
        array $headers = []
    ) {
        parent::__construct($statusCode, $data);
        $this->contentType = $contentType;
        $this->headers = (array)$headers;
    }

    public function getContentType(): string
    {
        return $this->contentType ? $this->contentType . '; charset=utf-8' : '';
    }

    public function getHeaders(): array
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
