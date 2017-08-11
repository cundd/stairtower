<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server\ValueObject;


use Cundd\Stairtower\Immutable;
use Cundd\Stairtower\Server\Handler\HandlerResultInterface;

/**
 * Exception Handler Result implementation
 */
class ExceptionResult implements HandlerResultInterface, Immutable
{
    private $exception;

    /**
     * ExceptionResult constructor
     *
     * @param $exception
     */
    public function __construct(\Exception $exception)
    {
        $this->exception = $exception;
    }


    public function getStatusCode(): int
    {
        return 500;
    }

    public function getData()
    {
        return $this->exception;
    }
}
