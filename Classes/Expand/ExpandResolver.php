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
use Cundd\PersistentObjectStore\Domain\Model\DocumentInterface;
use Cundd\PersistentObjectStore\Expand\Exception\ExpandException;
use Cundd\PersistentObjectStore\Filter\Comparison\ComparisonInterface;
use Cundd\PersistentObjectStore\Filter\Comparison\PropertyComparison;
use Cundd\PersistentObjectStore\Index\IndexableInterface;
use Cundd\PersistentObjectStore\Utility\ObjectUtility;

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

        // Get the local value
        $localKey = $configuration->getLocalKey();
        if (is_array($document)) {
            $localValue = ObjectUtility::valueForKeyPathOfObject($localKey, $document, self::NO_VALUE);
        } else {
            //$localValue = ObjectUtility::valueForKeyPathOfObject($localKey, $document->getData(), self::NO_VALUE);
            $localValue = $document->valueForKeyPath($localKey);
        }

        $foreignKey = $configuration->getForeignKey();

        // If a local value is found, look for the best search method for the foreign key and local value
        if ($localValue === null || $localValue === self::NO_VALUE) {
            $foreignValue = null;
        } elseif ($foreignKey === Constants::DATA_ID_KEY) {
            $foreignValue = $database->findByIdentifier($localValue);
        } elseif ($database instanceof IndexableInterface
            && $database->hasIndexesForValueOfProperty($localValue, $foreignKey)
        ) {
            $foreignValue = $database->queryIndexesForValueOfProperty($localValue, $foreignKey);
        } else {
            $foreignValue = $database->filter(
                new PropertyComparison($foreignKey, ComparisonInterface::TYPE_EQUAL_TO, $localValue)
            );
            $foreignValue = $foreignValue->toFixedArray();
            //$foreignValue = $foreignValue->valid() ? $foreignValue->current() : null;
        }

        // Retrieve the first value
        if ($foreignValue instanceof \SplFixedArray) {
            if ($foreignValue->getSize() > 0) {
                $foreignValue = $foreignValue[0];
            } else {
                $foreignValue = null;
            }
        }

        $propertyToSet = $configuration->getAsKey() ?: $localKey;
        ObjectUtility::setValueForKeyPathOfObject($foreignValue, $propertyToSet, $document);
        return !!$foreignValue;
    }
}