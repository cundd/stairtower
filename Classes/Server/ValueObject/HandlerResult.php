<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server\ValueObject;


use Cundd\Stairtower\Immutable;
use Cundd\Stairtower\Server\Handler\HandlerResultInterface;

/**
 * Handler result implementation
 */
class HandlerResult extends AbstractHandlerResult implements HandlerResultInterface, Immutable
{
}
