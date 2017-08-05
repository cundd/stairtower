<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 10.10.14
 * Time: 17:59
 */

namespace Cundd\PersistentObjectStore\Server\ValueObject;

use Cundd\PersistentObjectStore\Server\Handler\HandlerResultInterface;

/**
 * Interface for raw results that don't need to be formatted
 *
 * @package Cundd\PersistentObjectStore\Server\Handler
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
