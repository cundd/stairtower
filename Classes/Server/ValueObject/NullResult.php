<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server\ValueObject;


use Cundd\Stairtower\Immutable;
use Cundd\Stairtower\Server\Controller\ControllerResultInterface;

/**
 * Empty result implementation
 */
class NullResult implements ControllerResultInterface, Immutable
{
    /**
     * Returns the request's response data
     *
     * @return mixed
     */
    public function getData()
    {
        return null;
    }

    /**
     * Returns the status code for the response
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return 204;
    }

    public function getContentType(): string
    {
        return '';
    }

    public function getHeaders(): array
    {
        return [
            'Content-Length' => 0,
        ];
    }

}
