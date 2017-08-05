<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 29.12.14
 * Time: 15:55
 */

namespace Cundd\PersistentObjectStore\Expand;

use Cundd\PersistentObjectStore\Domain\Model\DocumentInterface;

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
     * @param DocumentInterface            $document
     * @param ExpandConfigurationInterface $configuration
     * @return boolean Returns if the Document has been expanded
     * @throws Exception\ExpandException
     */
    public function expandDocument($document, $configuration);

    /**
     * Expand the given Documents according to the given configuration
     *
     * @param DocumentInterface[]|\Traversable $documentCollection
     * @param ExpandConfigurationInterface     $configuration
     * @return boolean Returns if the Documents have been expanded
     * @throws Exception\ExpandException
     */
    public function expandDocumentCollection($documentCollection, $configuration);
}