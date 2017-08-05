<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Server\ValueObject;

use Cundd\PersistentObjectStore\Immutable;

/**
 * Raw result implementation that will not be formatted
 */
class RawResult extends AbstractHandlerResult implements RawResultInterface, Immutable
{
    /**
     * @var string
     */
    private $contentType;

    public function __construct($statusCode, $data = null, $contentType = 'application/octet-stream')
    {
        parent::__construct($statusCode, $data);
        $this->contentType = $contentType;
    }

    /**
     * @inheritdoc
     */
    public function getContentType()
    {
        return $this->contentType;
    }
}
