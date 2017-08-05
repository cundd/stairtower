<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 08.12.14
 * Time: 16:23
 */

namespace Cundd\PersistentObjectStore\Filter\Comparison;

use ReflectionClass;

/**
 * Helper to test and convert Comparison Types
 *
 * @package Cundd\PersistentObjectStore\Filter\Comparison
 */
class TypeHelper
{
    /**
     * Map of Dollar Comparison Type notation to the Comparison Interface Types
     *
     * @var array
     */
    public static $comparisonTypesMapDollar = array(
        '$eq'   => ComparisonInterface::TYPE_EQUAL_TO,
        '$ne'   => ComparisonInterface::TYPE_NOT_EQUAL_TO,
        '$lt'   => ComparisonInterface::TYPE_LESS_THAN,
        '$lte'  => ComparisonInterface::TYPE_LESS_THAN_OR_EQUAL_TO,
        '$gt'   => ComparisonInterface::TYPE_GREATER_THAN,
        '$gte'  => ComparisonInterface::TYPE_GREATER_THAN_OR_EQUAL_TO,
        '$lk'   => ComparisonInterface::TYPE_LIKE,
        '$con'  => ComparisonInterface::TYPE_CONTAINS,
        '$in'   => ComparisonInterface::TYPE_IN,
        '$null' => ComparisonInterface::TYPE_IS_NULL,
        '$em'   => ComparisonInterface::TYPE_IS_EMPTY,
        '$and'  => ComparisonInterface::TYPE_AND,
        '$or'   => ComparisonInterface::TYPE_OR,
    );

    /**
     * Returns if the given value is a Comparison Type
     *
     * @param string|int $input
     * @return bool
     */
    public function isComparisonType($input)
    {
        $comparisonInterfaceTypes = $this->getComparisonTypes();
        return in_array($input, $comparisonInterfaceTypes, true);
    }

    /**
     * Returns the Comparison Types
     *
     * @return array
     */
    public function getComparisonTypes()
    {
        static $comparisonInterfaceTypes;
        if (!$comparisonInterfaceTypes) {
            $reflectionClass          = new ReflectionClass('Cundd\\PersistentObjectStore\\Filter\\Comparison\\ComparisonInterface');
            $comparisonInterfaceTypes = $reflectionClass->getConstants();
        }
        return $comparisonInterfaceTypes;
    }

    /**
     * Returns if the given value is a Comparison Type
     *
     * @param string|int $input
     * @return bool
     */
    public function isComparisonTypeWithoutValue($input)
    {
        return $input === ComparisonInterface::TYPE_IS_NULL || $input === ComparisonInterface::TYPE_IS_EMPTY;
    }

    /**
     * Returns if the given value is a Logical Comparison Type
     *
     * @param string|int $input
     * @return bool
     */
    public function isLogicalComparisonType($input)
    {
        return $input === ComparisonInterface::TYPE_AND || $input === ComparisonInterface::TYPE_OR;
    }

    /**
     * Returns if the given value is a Property Comparison Type
     *
     * @param string|int $input
     * @return bool
     */
    public function isPropertyComparisonType($input)
    {
        return $this->isComparisonType($input)
        && $input !== ComparisonInterface::TYPE_OR
        && $input !== ComparisonInterface::TYPE_AND;
    }
}