<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Server\ValueObject;


use Cundd\PersistentObjectStore\Immutable;
use Cundd\PersistentObjectStore\Server\Handler\HandlerResultInterface;

/**
 * Handler result implementation
 */
class HandlerResult extends AbstractHandlerResult implements HandlerResultInterface, Immutable
{
}
