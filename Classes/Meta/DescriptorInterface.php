<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 25.02.15
 * Time: 21:08
 */

namespace Cundd\PersistentObjectStore\Meta;

/**
 * Interface for Descriptors
 *
 * @package Cundd\PersistentObjectStore\Meta
 */
interface DescriptorInterface
{
    /**
     * Returns the description of the subject
     *
     * @param mixed $subject
     * @return mixed
     */
    public function describe($subject);
}
