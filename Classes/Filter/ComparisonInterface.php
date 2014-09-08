<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 01.09.14
 * Time: 21:04
 */

namespace Cundd\PersistentObjectStore\Filter;

/**
 * Comparison interface
 *
 * @package Cundd\PersistentObjectStore\Filter
 */
interface ComparisonInterface {
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
	 * Gets the first operand
	 *
	 * @return mixed
	 */
	public function getProperty();

	/**
	 * Gets the operator
	 *
	 * @return string one of the TYPE constants
	 */
	public function getOperator();

	/**
	 * Gets the second operand
	 *
	 * @return mixed
	 */
	public function getValue();
}