<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 11.10.14
 * Time: 14:28
 */

namespace Cundd\PersistentObjectStore\Server\ValueObject;

use Cundd\PersistentObjectStore\Immutable;

/**
 * Raw result implementation that will not be formatted
 *
 * @package Cundd\PersistentObjectStore\Server\ValueObject
 */
class RawResult extends AbstractHandlerResult implements RawResultInterface, Immutable
{
}
