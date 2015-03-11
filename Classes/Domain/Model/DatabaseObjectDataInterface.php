<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 20.09.14
 * Time: 10:44
 */

namespace Cundd\PersistentObjectStore\Domain\Model;

/**
 * Special database interface that describes the access to objects by index
 *
 * @package Cundd\PersistentObjectStore\Domain\Model
 */
interface DatabaseObjectDataInterface
{
    /**
     * Returns the Document instance at the given index or sets it if it is not already set
     *
     * @param int $index
     * @return bool|DocumentInterface
     * @internal
     */
    public function getObjectDataForIndex($index);
}
