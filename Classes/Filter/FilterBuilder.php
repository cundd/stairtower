<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 14.10.14
 * Time: 20:15
 */

namespace Cundd\PersistentObjectStore\Filter;

use Cundd\PersistentObjectStore\Domain\Model\Database;
use Cundd\PersistentObjectStore\Filter\Comparison\ComparisonInterface;
use Cundd\PersistentObjectStore\Filter\Comparison\LogicalComparison;
use Cundd\PersistentObjectStore\Filter\Comparison\PropertyComparison;
use Cundd\PersistentObjectStore\Filter\Exception\InvalidFilterBuilderInputException;

/**
 * FilterBuild implementation
 *
 * @package Cundd\PersistentObjectStore\Filter
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
     * Build a Filter with the given query parts
     *
     * @param string[] $queryParts
     * @param Database $collection
     * @return FilterResult
     */
    public function buildFilterFromQueryParts($queryParts, $collection)
    {
        $comparisons = array();
        $this->getComparisonsFromArray($queryParts, $comparisons);
        $filter = new Filter($comparisons);
        return $filter->filterCollection($collection);
    }

    /**
     * Add Comparisons from the given array to the given comparison
     *
     * @param string[]              $queryParts
     * @param ComparisonInterface[] $comparisons
     * @return ComparisonInterface Returns the Comparison instance
     */
    public function getComparisonsFromArray($queryParts, &$comparisons = array())
    {
        foreach ($queryParts as $key => $value) {
            $comparisons[] = $this->getComparisonForValueAndKey($value, $key);
        };
        return $comparisons;
    }

    /**
     * Returns the Comparison instance for the value and key
     *
     * @param mixed  $value
     * @param string $key
     * @return LogicalComparison|PropertyComparison|null
     */
    public function getComparisonForValueAndKey($value, $key)
    {
        $comparison     = null;
        $comparisonType = null;
        if ($key === null || $key === '') {
            throw new InvalidFilterBuilderInputException('No key given', 1418043342);
        }

        /**
         * TODO: The Comparison Types need refactoring to avoid clashes with normal search value or property names
         */

        /*
         * If the value is an array it may contain a Comparison Type (e.g. 'greater than')
         */
        if (is_array($value)) {
            $arrayKey   = key($value);
            $arrayValue = current($value);

            if ($arrayKey[0] === '$') { // Check if the key's first character is a dollar
                $comparisonType = $this->comparisonTypeHelper->getComparisonTypeForDollarNotation($arrayKey);
            } elseif ($this->comparisonTypeHelper->isComparisonType($arrayKey)) { // Check if the key is a Comparison Type
                $comparisonType = $arrayKey;
            }

            if ($comparisonType) {
                $value = $arrayValue;
            }
        }

        /*
         * If the value isn't an array or the array does not contain a Comparison Type
         */
        if ($comparisonType === null) {
            if ($key[0] === '$') { // Check if the key's first character is a dollar
                $comparisonType = $this->comparisonTypeHelper->getComparisonTypeForDollarNotation($key);
            } elseif ($this->comparisonTypeHelper->isComparisonType($key)) { // Check if the key is a Comparison Type
                $comparisonType = $key;
            } elseif ($this->comparisonTypeHelper->isComparisonTypeWithoutValue($value)) {
                $comparisonType = $value;
            }
        }

        /*
         * If the value is a string starting with a dollar
         */
        if ($comparisonType === null) {
            if (is_string($value) && $value !== '' && $value[0] === '$'
                && $this->comparisonTypeHelper->isComparisonTypeWithoutValue($this->comparisonTypeHelper->getComparisonTypeForDollarNotation($value))
            ) {
                $comparisonType = $this->comparisonTypeHelper->getComparisonTypeForDollarNotation($value);
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
                $logicalComparisonConstraints = array();
                foreach ($value as $item) {
                    $this->getComparisonsFromArray($item, $logicalComparisonConstraints);
                    //$flatArray[key($item)] = current($item);
                }
                $comparison = new LogicalComparison($comparisonType, $logicalComparisonConstraints);
                $comparison->setStrict(true);
                //$comparison  = new LogicalComparison($comparisonType, $this->getComparisonsFromArray($value));
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
}