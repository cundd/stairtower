<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 29.12.14
 * Time: 15:55
 */

namespace Cundd\PersistentObjectStore\Expand;

use Cundd\PersistentObjectStore\Constants;
use Cundd\PersistentObjectStore\DataAccess\Exception\DataAccessException;
use Cundd\PersistentObjectStore\Domain\Model\DatabaseInterface;
use Cundd\PersistentObjectStore\Domain\Model\DocumentInterface;
use Cundd\PersistentObjectStore\Expand\Exception\ExpandException;
use Cundd\PersistentObjectStore\Filter\Comparison\ComparisonInterface;
use Cundd\PersistentObjectStore\Filter\Comparison\PropertyComparison;
use Cundd\PersistentObjectStore\Filter\Filter;
use Cundd\PersistentObjectStore\Filter\FilterResultInterface;
use Cundd\PersistentObjectStore\Index\IndexableInterface;
use Cundd\PersistentObjectStore\Utility\ObjectUtility;
use SplFixedArray;

/**
 * Class that will fetch the Documents and set the property according to a Expand configuration
 *
 * @package Cundd\PersistentObjectStore\Expand
 */
class ExpandResolver implements ExpandResolverInterface
{
    /**
     * Constant to use if a key path could not be resolved
     */
    const NO_VALUE = '--no-value-for-this-key-cundd-stairtower';

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
     * @param DocumentInterface            $document
     * @param ExpandConfigurationInterface $configuration
     * @return boolean Returns if the Document has been expanded
     * @throws Exception\ExpandException
     */
    public function expandDocument($document, $configuration)
    {
        // Try to get the Database before checking other values
        try {
            $database = $this->coordinator->getDatabase($configuration->getDatabaseIdentifier());
        } catch (DataAccessException $dataAccessException) {
            throw new ExpandException(
                $dataAccessException->getMessage(),
                $dataAccessException->getCode(),
                $dataAccessException
            );
        }

        $localKey      = $configuration->getLocalKey();
        $propertyToSet = $configuration->getAsKey() ?: $localKey;

        // Get the local value
        if (is_array($document)) {
            $localValue = ObjectUtility::valueForKeyPathOfObject($localKey, $document, self::NO_VALUE);
        } else {
            //$localValue = ObjectUtility::valueForKeyPathOfObject($localKey, $document->getData(), self::NO_VALUE);
            $localValue = $document->valueForKeyPath($localKey);
        }

        $foreignValue = $this->getValueFromCollection($database, $configuration, $localValue);
        ObjectUtility::setValueForKeyPathOfObject($foreignValue, $propertyToSet, $document);
        return !!$foreignValue;
    }

    /**
     * Expand the given Documents according to the given configuration
     *
     * @param DocumentInterface[]|\Traversable $documentCollection
     * @param ExpandConfigurationInterface     $configuration
     * @return boolean Returns if the Documents have been expanded
     * @throws Exception\ExpandException
     */
    public function expandDocumentCollection($documentCollection, $configuration)
    {
        $fixedCollection = null;
        if (is_array($documentCollection)) {
            $fixedCollection = SplFixedArray::fromArray($documentCollection);
        } elseif ($documentCollection instanceof SplFixedArray) {
            $fixedCollection = $documentCollection;
        } elseif ($documentCollection instanceof \Traversable) {
            $fixedCollection = iterator_to_array($documentCollection);
        }

        $success                = true;
        $localKey               = $configuration->getLocalKey();
        $propertyToSet          = $configuration->getAsKey() ?: $localKey;
        $fixedCollectionCount   = $fixedCollection->count();
        $foreignValueCollection = $this->collectRelatedDocuments(
            $fixedCollection,
            $configuration,
            $localValueCollection
        );


        // Loop through the Documents and expand each one
        $i = 0;
        do {
            $currentDocument = $fixedCollection[$i];
            $localValue      = $localValueCollection[$i];

            $foreignValue = $this->getValueFromCollection($foreignValueCollection, $configuration, $localValue);

            ObjectUtility::setValueForKeyPathOfObject($foreignValue, $propertyToSet, $currentDocument);

            $success *= (!!$foreignValue);
        } while (++$i < $fixedCollectionCount);
        return !!$success;
    }

    /**
     * Collect the related Documents for the given Documents according to the given configuration
     *
     * @param SplFixedArray                $fixedCollection
     * @param ExpandConfigurationInterface $configuration
     * @param SplFixedArray                $localValueCollection Reference to be filled with the local values
     * @return FilterResultInterface
     */
    public function collectRelatedDocuments($fixedCollection, $configuration, &$localValueCollection = null)
    {
        // Try to get the Database before checking other values
        try {
            $database = $this->coordinator->getDatabase($configuration->getDatabaseIdentifier());
        } catch (DataAccessException $dataAccessException) {
            throw new ExpandException(
                $dataAccessException->getMessage(),
                $dataAccessException->getCode(),
                $dataAccessException
            );
        }

        // Get all the local values
        $i                    = 0;
        $localKey             = $configuration->getLocalKey();
        $foreignKey           = $configuration->getForeignKey();
        $fixedCollectionCount = $fixedCollection->count();
        $localValueCollection = new SplFixedArray($fixedCollectionCount);
        do {
            $currentDocument = $fixedCollection[$i];
            if (is_array($currentDocument)) {
                $localValueCollection[$i] = ObjectUtility::valueForKeyPathOfObject($localKey, $currentDocument,
                    self::NO_VALUE);
            } else {
                $localValueCollection[$i] = $currentDocument->valueForKeyPath($localKey);
            }
        } while (++$i < $fixedCollectionCount);


        // Get the Documents from the Database that match the values
        return $database->filter(
            new PropertyComparison($foreignKey, ComparisonInterface::TYPE_IN, $localValueCollection)
        );
    }

    /**
     * @param DatabaseInterface|FilterResultInterface $collection
     * @param ExpandConfigurationInterface            $configuration
     * @param mixed                                   $localValue
     * @return mixed
     */
    protected function getValueFromCollection($collection, $configuration, $localValue)
    {
        $foreignKey = $configuration->getForeignKey();

        // If a local value is found, look for the best search method for the foreign key and local value
        if ($localValue === null || $localValue === self::NO_VALUE) {
            $foreignValue = null;
        } elseif ($foreignKey === Constants::DATA_ID_KEY) {
            $foreignValue = $collection->findByIdentifier($localValue);
        } elseif ($collection instanceof IndexableInterface
            && $collection->hasIndexesForValueOfProperty($localValue, $foreignKey)
        ) {
            $foreignValue = $collection->queryIndexesForValueOfProperty($localValue, $foreignKey);
        } else {
            $filter       = new Filter(
                new PropertyComparison($foreignKey, ComparisonInterface::TYPE_EQUAL_TO, $localValue)
            );
            $foreignValue = $filter->filterCollection($collection);

            // If expand to many is not defined only the first result has to be queried
            if (!$configuration->getExpandToMany()) {
                return $foreignValue->valid() ? $foreignValue->current() : null;
            } else {
                return $foreignValue->toFixedArray();
            }
        }

        // Check if many objects are expected
        if ($configuration->getExpandToMany()) {
            if (!$foreignValue instanceof \Traversable) {
                $foreignValue = array($foreignValue);
                return $foreignValue;
            }
            return $foreignValue;
        } elseif ($foreignValue instanceof SplFixedArray) { // Retrieve the first value
            if ($foreignValue->getSize() > 0) {
                $foreignValue = $foreignValue[0];
                return $foreignValue;
            } else {
                $foreignValue = null;
                return $foreignValue;
            }
        }
        return $foreignValue;
    }
}