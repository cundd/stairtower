<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server\ValueObject;

use Cundd\Stairtower\Immutable;
use Cundd\Stairtower\Server\Controller\ControllerResultInterface;

/**
 * Controller result implementation
 */
class ControllerResult extends AbstractControllerResult implements ControllerResultInterface, Immutable
{
}
