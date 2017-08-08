<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server\ValueObject;


use Cundd\Stairtower\Server\Handler\HandlerResultInterface;

/**
 * Handler result implementation
 */
class MutableHandlerResult extends AbstractHandlerResult implements HandlerResultInterface
{
    /**
     * Sets the status code for the response
     *
     * @param int $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * Sets the request's response data
     *
     * @param mixed $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

}
