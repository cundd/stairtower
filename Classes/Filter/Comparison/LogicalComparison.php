<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 13.09.14
 * Time: 19:25
 */

namespace Cundd\PersistentObjectStore\Filter\Comparison;


use Cundd\PersistentObjectStore\Domain\Model\DataInterface;
use Cundd\PersistentObjectStore\Filter\Comparison\PropertyComparisonInterface;
use Cundd\PersistentObjectStore\Filter\Exception\InvalidComparisonException;
use Cundd\PersistentObjectStore\Utility\DebugUtility;
use Cundd\PersistentObjectStore\Utility\ObjectUtility;

/**
 * Nested logical comparison
 *
 * @package Cundd\PersistentObjectStore\Filter\Comparison
 */
class LogicalComparison implements LogicalComparisonInterface {
	/**
	 * Collection of constraints
	 *
	 * @var array
	 */
	protected $constraints = array();

	/**
	 * Type of the comparison from the comparison value against the given test data's property
	 *
	 * @var string One of the ComparisonInterface::TYPE constants
	 */
	protected $operator;

	/**
	 * Creates a new comparison
	 *
	 * @param string $operator One of the ComparisonInterface::TYPE constants
	 * @param array|ComparisonInterface $constraints
	 */
	function __construct($operator, $constraints) {
		$this->operator = $operator;

		if (func_num_args() > 2) {
			$arguments = func_get_args();
			array_shift($arguments);
			$this->constraints = $arguments;
		} else if ($constraints) {
			$this->constraints = $constraints;
		}
	}

	/**
	 * Returns the type of the comparison from the comparison value against the given test data's property
	 *
	 * @return string one of the TYPE constants
	 */
	public function getOperator() {
		return $this->operator;
	}

	/**
	 * Returns the constraints
	 *
	 * @return array|\Iterator
	 */
	public function getConstraints() {
		return $this->constraints;
	}

	/**
	 * Performs the comparison against the given test value
	 *
	 * @param mixed $testValue
	 * @throws \Cundd\PersistentObjectStore\Filter\Exception\InvalidComparisonException
	 * @return bool
	 */
	public function perform($testValue) {
		$operator = $this->getOperator();
		$isOr = $operator === ComparisonInterface::TYPE_OR;
		if ($operator !== ComparisonInterface::TYPE_AND && $operator !== ComparisonInterface::TYPE_OR) {
			throw new InvalidComparisonException(sprintf('Can not perform logical comparison with operator %s', $operator), 1410704637);
		}

		$constraints = $this->getConstraints();
		if (!$constraints) throw new InvalidComparisonException('No constraints given', 1410710918);


		foreach ($constraints as $constraint) {
			// If the operator is OR and one constraint is TRUE return TRUE
			if ($isOr && ($constraint instanceof ComparisonInterface ? $constraint->perform($testValue) : $constraint)) {
				return TRUE;
			}

			// If the operator is AND and one constraint is FALSE return FALSE
			if (!$isOr && !($constraint instanceof ComparisonInterface ? $constraint->perform($testValue) : $constraint)) {
				return FALSE;
			}
		}


		if ($isOr) { // If the operator is OR and none matched so far, return FALSE
			return FALSE;
		}

		// If the operator is AND and nothing failed, return TRUE
		return TRUE;
	}

//	/**
//	 * Performs the given logical comparison
//	 *
//	 * @param mixed  $testValue
//	 * @param string $operator
//	 * @throws InvalidComparisonException if the given operator is neither ComparisonInterface::TYPE_AND nor ComparisonInterface::TYPE_OR
//	 * @return bool
//	 */
//	protected function performLogical($testValue, $operator) {
//		$expression1 = $this->property;
//		$expression2 = $this->value;
//
//		$result1 = (bool)($expression1 instanceof PropertyComparisonInterface ? $expression1->perform($testValue) : $expression1);
//
//		if ($operator === PropertyComparisonInterface::TYPE_AND) {
//			$result2 = (bool)($expression2 instanceof PropertyComparisonInterface ? $expression2->perform($testValue) : $expression2);
//			return $result1 && $result2;
//		} else if ($operator === PropertyComparisonInterface::TYPE_OR) {
//			if ($result1) {
//				return $result1;
//			}
//			return (bool)($expression2 instanceof PropertyComparisonInterface ? $expression2->perform($testValue) : $expression2);
//		}
//		throw new InvalidComparisonException(sprintf('Can not perform logical comparison with operator %s', $operator), 1410704637);
//	}
}