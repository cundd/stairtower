<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Server\ValueObject;

use Cundd\PersistentObjectStore\Immutable;
use Cundd\PersistentObjectStore\Server\Controller\ControllerResultInterface;

/**
 * Controller result implementation
 */
class ControllerResult extends AbstractControllerResult implements ControllerResultInterface, Immutable
{
}
