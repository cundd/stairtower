<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 29.12.14
 * Time: 15:55
 */

namespace Cundd\PersistentObjectStore\Expand;

use Cundd\PersistentObjectStore\Domain\Model\Document;

/**
 * Interface for the class that will fetch the Documents and set the property according to a Expand configuration
 *
 * @package Cundd\PersistentObjectStore\Expand
 */
interface ExpandResolverInterface
{
    /**
     * Expand the given Document according to the given configuration
     *
     * @param Document                     $document
     * @param ExpandConfigurationInterface $configuration
     * @return void
     * @throws Exception\ExpandException
     */
    public function expandDocument($document, $configuration);
}