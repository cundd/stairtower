<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server\ValueObject;

use Cundd\Stairtower\Server\Handler\HandlerResultInterface;

/**
 * Interface for raw results that don't need to be formatted
 */
interface RawResultInterface extends HandlerResultInterface
{
    /**
     * Returns the content type of the result
     *
     * @return string
     */
    public function getContentType();
}
