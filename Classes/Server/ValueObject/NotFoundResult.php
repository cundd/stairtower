<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server\ValueObject;

use Cundd\Stairtower\Server\Handler\HandlerResultInterface;

class NotFoundResult implements HandlerResultInterface
{
    private $message = '';
    private $exceptionCode = 0;

    /**
     * NotFoundResult constructor
     *
     * @param string $message
     * @param int    $exceptionCode
     */
    public function __construct(string $message, int $exceptionCode)
    {
        $this->message = $message;
        $this->exceptionCode = $exceptionCode;
    }


    public function getStatusCode(): int
    {
        return 404;
    }

    public function getData()
    {
        return (string)$this->message;
    }
}
