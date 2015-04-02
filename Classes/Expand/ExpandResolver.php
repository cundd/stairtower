<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 29.12.14
 * Time: 15:55
 */

namespace Cundd\PersistentObjectStore\Expand;

use ArrayObject;
use Cundd\PersistentObjectStore\Constants;
use Cundd\PersistentObjectStore\DataAccess\Exception\DataAccessException;
use Cundd\PersistentObjectStore\Domain\Model\DatabaseInterface;
use Cundd\PersistentObjectStore\Domain\Model\DocumentInterface;
use Cundd\PersistentObjectStore\Expand\Exception\ExpandException;
use Cundd\PersistentObjectStore\Expand\Exception\InvalidExpandInputException;
use Cundd\PersistentObjectStore\Filter\Comparison\ComparisonInterface;
use Cundd\PersistentObjectStore\Filter\Comparison\PropertyComparison;
use Cundd\PersistentObjectStore\Filter\Filter;
use Cundd\PersistentObjectStore\Filter\FilterResultInterface;
use Cundd\PersistentObjectStore\Index\IndexableInterface;
use Cundd\PersistentObjectStore\Utility\DebugUtility;
use Cundd\PersistentObjectStore\Utility\GeneralUtility;
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
     * Document Access Coordinator
     *
     * @var \Cundd\PersistentObjectStore\DataAccess\CoordinatorInterface
     * @Inject
     */
    protected $coordinator;

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
            $localValue = ObjectUtility::valueForKeyPathOfObject($localKey, $document);
        } else {
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
            $fixedCollection = SplFixedArray::fromArray(iterator_to_array($documentCollection));
        } else {
            throw new InvalidExpandInputException(
                sprintf(
                    'Given document collection is of unsupported type %s',
                    GeneralUtility::getType($documentCollection)
                ),
                1423208941
            );
        }

        $success                = true;
        $localKey               = $configuration->getLocalKey();
        $propertyToSet          = $configuration->getAsKey() ?: $localKey;
        $fixedCollectionCount   = $fixedCollection->count();
        $foreignValueCollection = $this->collectRelatedDocuments(
            $fixedCollection,
            $configuration,
            $localValueCollection,
            $localValuesAreScalar
        );

        // Loop through the Documents and expand each one
        $i = 0;
        do {
            $currentDocument = $fixedCollection[$i];
            $localValue      = $localValueCollection[$i];

            // If $foreignValueCollection is false or the local value is null the foreign value has to be null
            if ($foreignValueCollection === false || $localValue === null) {
                $foreignValue = null;
            } else {
                $foreignValue = $this->getValueFromCollection($foreignValueCollection, $configuration, $localValue);
            }
            ObjectUtility::setValueForKeyPathOfObject($foreignValue, $propertyToSet, $currentDocument);

            $success *= (!!$foreignValue);
        } while (++$i < $fixedCollectionCount);
        return !!$success;
    }

    /**
     * Collect the related Documents for the given Documents according to the given configuration
     *
     * This method has different return types:
     * 1. ArrayObject: A dictionary is returned with the foreign value as key and an array as value
     *
     *  Example:
     *  ArrayObject(
     *      'brown' => array(
     *          ... an array of persons with brown hair
     *      )
     *      'green' => array(
     *          ... an array of persons with brown hair
     *      )
     *  )
     *
     * 2. FilterResultInterface: A Filter Result with all foreign values
     *
     * 3. bool: Returns false if none of the Documents in the collection have a value for the local key from the configuration
     *
     *
     * @param SplFixedArray                $fixedCollection
     * @param ExpandConfigurationInterface $configuration
     * @param SplFixedArray                $localValueCollection Reference to be filled with the local values
     * @param bool $localValuesAreScalar Reference to be set to true if all local values are scalar
     * @return ArrayObject|bool|FilterResultInterface
     */
    public function collectRelatedDocuments(
        $fixedCollection,
        $configuration,
        &$localValueCollection = null,
        &$localValuesAreScalar = true
    ) {
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

        $i          = 0;
        $localKey   = $configuration->getLocalKey();
        $foreignKey = $configuration->getForeignKey();

        // Get all the local values
        $returnDictionary = true;
        $didSetAValue     = false;
        $fixedCollectionCount = $fixedCollection->count();
        $localValueCollection = new SplFixedArray($fixedCollectionCount);
        do {
            $currentDocument = $fixedCollection[$i];
            if (is_array($currentDocument)) {
                $localValue = ObjectUtility::valueForKeyPathOfObject($localKey, $currentDocument);
            } else {
                $localValue = $currentDocument->valueForKeyPath($localKey);
            }

            // Don't add null to the collection
            if ($localValue === null) {
                continue;
            }

            if (!$didSetAValue) {
                $didSetAValue = true;
            }

            // Swap $returnDictionary to false if the local value is no scalar
            if ($returnDictionary && !is_scalar($localValue)) {
                $localValuesAreScalar = false;
                $returnDictionary     = false;
            }

            $localValueCollection[$i] = $localValue;

        } while (++$i < $fixedCollectionCount);

        if (!$didSetAValue) {
            return false;
        }

        // Get the Documents from the Database that match the values
        // If the possible values are all of scalar types remove duplicate values
        if ($localValuesAreScalar) {
            $possibleValueCollection = SplFixedArray::fromArray(array_unique($localValueCollection->toArray()));
        } else {
            $possibleValueCollection = $localValueCollection;
        }

        $filter       = new Filter(new PropertyComparison(
            $foreignKey,
            ComparisonInterface::TYPE_IN,
            $possibleValueCollection
        ));
        $filterResult = $filter->filterCollection($database);
        if (!$returnDictionary) {
            return $filterResult;
        }
        return $this->transformFilterResultToDictionaryWithKey($filterResult, $foreignKey);
    }

    /**
     * Transforms the given Filter Result into a dictionary
     *
     * @param FilterResultInterface $filterResult Filter Result to transform
     * @param string                $key          Property of the Filter Result's Documents that will be used a key for the dictionary
     * @return ArrayObject
     */
    protected function transformFilterResultToDictionaryWithKey($filterResult, $key)
    {
        $i                      = 0;
        $dictionary             = new ArrayObject();
        $fixedFilterResult      = $filterResult->toFixedArray();
        $fixedFilterResultCount = $fixedFilterResult->count();
        if ($fixedFilterResultCount === 0) {
            return $dictionary;
        }
        do {
            $currentDocument = $fixedFilterResult[$i];
            if (!$currentDocument instanceof DocumentInterface) {
                throw new ExpandException(
                    sprintf(
                        'Value expected to be a Document %s given',
                        is_object($currentDocument) ? get_class($currentDocument) : gettype($currentDocument)
                    ),
                    1420290151
                );
            }
            $dictionary[$currentDocument->valueForKeyPath($key)][] = $currentDocument;
        } while (++$i < $fixedFilterResultCount);
        return $dictionary;
    }

    /**
     * @param DatabaseInterface|FilterResultInterface|array $collection
     * @param ExpandConfigurationInterface                  $configuration
     * @param mixed                                         $localValue
     * @return mixed
     */
    protected function getValueFromCollection($collection, $configuration, $localValue)
    {
        $foreignKey = $configuration->getForeignKey();

        // If a local value is found, look for the best search method for the foreign key and local value
        if ($localValue === null) {
            $foreignValue = null;
        } elseif (is_scalar($localValue) && $collection instanceof ArrayObject) {
            $foreignValue = $collection->offsetExists($localValue) ? $collection[$localValue] : null;
        } elseif (is_scalar($localValue) && is_array($collection) && isset($collection[$localValue])) {
            $foreignValue = $collection[$localValue];
        } elseif ($foreignKey === Constants::DATA_ID_KEY && $collection instanceof DatabaseInterface) {
            $foreignValue = $collection->findByIdentifier($localValue);
        } elseif ($collection instanceof IndexableInterface
            && $collection->hasIndexesForValueOfProperty($localValue, $foreignKey)
        ) {
            $foreignValue = $collection->queryIndexesForValueOfProperty($localValue, $foreignKey);
        } elseif ($collection) {
            $filter = new Filter(new PropertyComparison(
                $foreignKey,
                ComparisonInterface::TYPE_EQUAL_TO,
                $localValue
            ));
            $foreignValue = $filter->filterCollection($collection);

            // If expand to many is not defined only the first result has to be queried
            if (!$configuration->getExpandToMany()) {
                return $foreignValue->valid() ? $foreignValue->current() : null;
            } else {
                return $foreignValue->toFixedArray();
            }
        } elseif (is_array($collection) && empty($collection)) { // If empty array
            return null;
        } else {
            DebugUtility::var_dump($collection);
            throw new ExpandException('No collection given', 1420290357);
        }

        // Check if many objects are expected
        if ($configuration->getExpandToMany()) {
            if (!$foreignValue instanceof \Traversable && !is_array($foreignValue)) {
                $foreignValue = array($foreignValue);
                return $foreignValue;
            }
            return $foreignValue;
        } elseif ($foreignValue instanceof SplFixedArray) { // Retrieve the first value
            if ($foreignValue->getSize() > 0) {
                $foreignValue = $foreignValue[0];
                return $foreignValue;
            } else {
                return null;
            }
        } elseif (is_array($foreignValue)) {
            return reset($foreignValue);
        }
        return $foreignValue;
    }
}