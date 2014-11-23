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
    const TYPE_EQUAL_TO = '==';

    /**
     * The '!=' comparison operator
     *
     * @api
     */
    const TYPE_NOT_EQUAL_TO = '!=';

    /**
     * The '<' comparison operator
     *
     * @api
     */
    const TYPE_LESS_THAN = '<';

    /**
     * The '<=' comparison operator
     *
     * @api
     */
    const TYPE_LESS_THAN_OR_EQUAL_TO = '<=';

    /**
     * The '>' comparison operator
     *
     * @api
     */
    const TYPE_GREATER_THAN = '>';

    /**
     * The '>=' comparison operator
     *
     * @api
     */
    const TYPE_GREATER_THAN_OR_EQUAL_TO = '>=';

    /**
     * The 'like' comparison operator
     *
     * @api
     */
    const TYPE_LIKE = '><';

    /**
     * The 'contains' comparison operator for collections
     *
     * @api
     */
    const TYPE_CONTAINS = 8;

    /**
     * The 'in' comparison operator
     *
     * @api
     */
    const TYPE_IN = 9;

    /**
     * The 'is NULL' comparison operator
     *
     * @api
     */
    const TYPE_IS_NULL = 10;

    /**
     * The 'is empty' comparison operator for collections
     *
     * @api
     */
    const TYPE_IS_EMPTY = 11;

    /**
     * The 'and' comparison operator for collections
     *
     * @api
     */
    const TYPE_AND = 'AND';

    /**
     * The 'or' comparison operator for collections
     *
     * @api
     */
    const TYPE_OR = 'OR';

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