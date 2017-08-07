<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Filter;

use Cundd\PersistentObjectStore\Filter\Comparison\ComparisonInterface;
use Cundd\PersistentObjectStore\Filter\Comparison\LogicalComparison;
use Cundd\PersistentObjectStore\Filter\Comparison\PropertyComparison;
use Cundd\PersistentObjectStore\Filter\Exception\InvalidFilterBuilderInputException;
use Cundd\PersistentObjectStore\Utility\DebugUtility;

/**
 * FilterBuild implementation
 */
class FilterBuilder implements FilterBuilderInterface
{
    /**
     * Default Comparison type to use if none is explicitly defined
     *
     * @var string
     */
    protected $defaultComparisonType = ComparisonInterface::TYPE_LIKE;

    /**
     * @var \Cundd\PersistentObjectStore\Filter\Comparison\TypeHelper
     * @inject
     */
    protected $comparisonTypeHelper;

    /**
     * Dictionary of cached filters
     *
     * @var FilterInterface[]
     */
    protected $cachedFilters = [];

    public function buildFilter(array $filterDefinition): FilterInterface
    {
        $cachedFilter = $this->getCachedFilterForFilterDefinition($filterDefinition);
        if ($cachedFilter) {
            return $cachedFilter;
        }

        $comparison = $this->getComparisonFromArray($filterDefinition);
        $newFilter = new Filter($comparison);
        $this->setCachedFilterForFilterDefinition($newFilter, $filterDefinition);

        return $newFilter;
    }

    /**
     * Add Comparisons from the given array to the given comparison
     *
     * @param string[] $filterDefinition
     * @return ComparisonInterface Returns the Comparison instance
     */
    public function getComparisonFromArray(array $filterDefinition)
    {
        $comparisons = [];
        if (!is_array($filterDefinition) && !$filterDefinition instanceof \Traversable) {
            DebugUtility::var_dump($filterDefinition);
            throw new InvalidFilterBuilderInputException(
                sprintf(
                    'Given value of type %s is not traversable',
                    is_object($filterDefinition) ? get_class($filterDefinition) : gettype($filterDefinition)
                ),
                1418495499
            );
        }
        foreach ($filterDefinition as $key => $value) {
            $comparisons[] = $this->getComparisonForValueAndKey($value, $key);
        }

        return new LogicalComparison(ComparisonInterface::TYPE_AND, ...$comparisons);
    }

    /**
     * Returns the Comparison instance for the value and key
     *
     * @param mixed  $value
     * @param string $key
     * @return LogicalComparison|PropertyComparison|null
     */
    public function getComparisonForValueAndKey($value, string $key)
    {
        $comparison = null;
        $comparisonType = null;
        if ($key === null || $key === '') {
            throw new InvalidFilterBuilderInputException('No key given', 1418043342);
        }

        /*
         * If the value isn't an array or the array does not contain a Comparison Type
         */
        if ($comparisonType === null) {
            if ($key[0] === '$' && $this->comparisonTypeHelper->isComparisonType(
                    $key
                )) { // Check if the key's first character is a dollar
                $comparisonType = $key;
            } elseif (is_string($value) && $value !== '' && $value[0] === '$'
                && $value[0] === $this->comparisonTypeHelper->isComparisonTypeWithoutValue($value)
            ) {
                $comparisonType = $value;
            }
        }

        /*
         * If the value is an array it may contain a Comparison Type (e.g. 'greater than')
         */
        if ($comparisonType === null) {
            if (is_array($value)) {
                $arrayKey = key($value);
                $arrayValue = current($value);

                if ($arrayKey[0] === '$' && $this->comparisonTypeHelper->isComparisonType(
                        $arrayKey
                    )) { // Check if the key's first character is a dollar
                    $comparisonType = $arrayKey;
                    $value = $arrayValue;
                }
            }
        }

        /*
         * If the value is a string starting with a dollar
         */
        if ($comparisonType === null) {
            if (is_string($value) && $value !== '' && $value[0] === '$'
                && $this->comparisonTypeHelper->isComparisonTypeWithoutValue($value)
            ) {
                $comparisonType = $value;
            }
        }

        if ($comparisonType === null) {
            $comparisonType = $this->defaultComparisonType;
        }
        switch ($comparisonType) {
            case ComparisonInterface::TYPE_EQUAL_TO:
            case ComparisonInterface::TYPE_NOT_EQUAL_TO:
            case ComparisonInterface::TYPE_LESS_THAN:
            case ComparisonInterface::TYPE_LESS_THAN_OR_EQUAL_TO:
            case ComparisonInterface::TYPE_GREATER_THAN:
            case ComparisonInterface::TYPE_GREATER_THAN_OR_EQUAL_TO:
            case ComparisonInterface::TYPE_LIKE:
            case ComparisonInterface::TYPE_CONTAINS:
            case ComparisonInterface::TYPE_IN:
            case ComparisonInterface::TYPE_IS_NULL:
            case ComparisonInterface::TYPE_IS_EMPTY:
                $comparison = new PropertyComparison($key, $comparisonType, $value);
                break;

            case ComparisonInterface::TYPE_AND:
            case ComparisonInterface::TYPE_OR:
                if (!is_array($value) || $value instanceof \Traversable) {
                    throw new Exception\InvalidFilterBuilderInputException(
                        sprintf(
                            'Value of the current %s constraint is neither an array nor a Traversable, but an instance of %s',
                            $comparisonType,
                            is_object($value) ? get_class($value) : gettype($value)
                        ),
                        1418036311
                    );
                }
                $logicalComparisonConstraints = [];
                foreach ($value as $subKey => $subValue) {
                    if (is_int($subKey) && (is_array($subValue) || $subValue instanceof \Traversable)) {
                        $logicalComparisonConstraints[] = $this->getComparisonFromArray($subValue);
                    } else {
                        $logicalComparisonConstraints[] = $this->getComparisonForValueAndKey($subValue, $subKey);
                    }
                }
                $comparison = new LogicalComparison($comparisonType, ...$logicalComparisonConstraints);
                $comparison->setStrict(true);
                break;

            default:
                throw new Exception\InvalidFilterBuilderInputException(
                    sprintf(
                        'Invalid Comparison Type %s for key %s and value of type %s',
                        $comparisonType,
                        $key,
                        is_object($value) ? get_class($value) : gettype($value)
                    ),
                    1418057191
                );

        }

        return $comparison;
    }

    /**
     * Checks for a cached Filter for the given filter definition
     *
     * @param array $filterDefinition
     * @return FilterInterface|null Returns the cached Filter or null if none was found
     */
    protected function getCachedFilterForFilterDefinition(array $filterDefinition): ?FilterInterface
    {
        $filterKey = json_encode($filterDefinition);
        if (!$filterKey) {
            return null;
        }

        return $this->cachedFilters[$filterKey] ?? null;
    }

    /**
     * Adds the Filter to the cache for the given filter definition
     *
     * @param FilterInterface $filter
     * @param array           $filterDefinition
     * @return void
     */
    protected function setCachedFilterForFilterDefinition(FilterInterface $filter, array $filterDefinition)
    {
        $filterKey = json_encode($filterDefinition);
        if ($filterKey) {
            $this->cachedFilters[$filterKey] = $filter;
        }
    }
}