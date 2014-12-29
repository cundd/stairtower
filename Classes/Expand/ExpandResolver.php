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
 * Class that will fetch the Documents and set the property according to a Expand configuration
 *
 * @package Cundd\PersistentObjectStore\Expand
 */
class ExpandResolver implements ExpandResolverInterface
{
    /**
     * Document Access Coordinator
     *
     * @var \Cundd\PersistentObjectStore\DataAccess\CoordinatorInterface
     * @Inject
     */
    protected $coordinator;

    /**
     * Returns the Document Access Coordinator
     *
     * @return \Cundd\PersistentObjectStore\DataAccess\CoordinatorInterface
     */
    public function getCoordinator()
    {
        return $this->coordinator;
    }

    /**
     * Expand the given Document according to the given configuration
     *
     * @param Document                     $document
     * @param ExpandConfigurationInterface $configuration
     * @return void
     * @throws Exception\ExpandException
     */
    public function expandDocument($document, $configuration)
    {
        // TODO: Implement expandDocument() method.
    }


}