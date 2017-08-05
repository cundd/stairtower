<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 11.10.14
 * Time: 14:28
 */

namespace Cundd\PersistentObjectStore\Server\ValueObject;

use Cundd\PersistentObjectStore\Immutable;
use Cundd\PersistentObjectStore\Server\Controller\ControllerResultInterface;

/**
 * Controller result implementation
 *
 * @package Cundd\PersistentObjectStore\Server\ValueObject
 */
class ControllerResult extends AbstractControllerResult implements ControllerResultInterface, Immutable
{
}
