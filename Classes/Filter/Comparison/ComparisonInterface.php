<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 01.09.14
 * Time: 21:04
 */

namespace Cundd\PersistentObjectStore\Filter\Comparison;

/**
 * Comparison interface
 *
 * @package Cundd\PersistentObjectStore\Filter
 */
interface ComparisonInterface
{
    /**
     * The '=' comparison operator
     *
     * @api
     */
    const TYPE_EQUAL_TO = '$eq';

    /**
     * The '!=' comparison operator
     *
     * @api
     */
    const TYPE_NOT_EQUAL_TO = '$ne';

    /**
     * The '<' comparison operator
     *
     * @api
     */
    const TYPE_LESS_THAN = '$lt';

    /**
     * The '<=' comparison operator
     *
     * @api
     */
    const TYPE_LESS_THAN_OR_EQUAL_TO = '$lte';

    /**
     * The '>' comparison operator
     *
     * @api
     */
    const TYPE_GREATER_THAN = '$gt';

    /**
     * The '>=' comparison operator
     *
     * @api
     */
    const TYPE_GREATER_THAN_OR_EQUAL_TO = '$gte';

    /**
     * The 'like' comparison operator
     *
     * @api
     */
    const TYPE_LIKE = '$lk';

    /**
     * The 'contains' comparison operator for collections
     *
     * @api
     */
    const TYPE_CONTAINS = '$con';

    /**
     * The 'in' comparison operator
     *
     * @api
     */
    const TYPE_IN = '$in';

    /**
     * The 'is NULL' comparison operator
     *
     * @api
     */
    const TYPE_IS_NULL = '$null';

    /**
     * The 'is empty' comparison operator for collections
     *
     * @api
     */
    const TYPE_IS_EMPTY = '$em';

    /**
     * The 'and' comparison operator for collections
     *
     * @api
     */
    const TYPE_AND = '$and';

    /**
     * The 'or' comparison operator for collections
     *
     * @api
     */
    const TYPE_OR = '$or';

    /**
     * Performs the comparison against the given test value
     *
     * @param mixed $testValue
     * @return bool
     */
    public function perform($testValue);

    /**
     * Returns the type of the comparison from the comparison value against the given test data's property
     *
     * @return string one of the TYPE constants
     */
    public function getOperator();
}