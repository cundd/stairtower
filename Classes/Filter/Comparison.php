<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 01.09.14
 * Time: 21:45
 */

namespace Cundd\PersistentObjectStore\Filter;


/**
 * Comparison implementation
 *
 * @package Cundd\PersistentObjectStore\Filter
 */
class Comparison implements ComparisonInterface {
	/**
	 * @var mixed
	 */
	protected $operand1;

	/**
	 * @var mixed
	 */
	protected $operand2;

	/**
	 * @var string One of the ComparisonInterface::TYPE constants
	 */
	protected $operator;

	/**
	 * Creates a new comparison
	 *
	 * @param mixed  $operand1
	 * @param string $operator One of the ComparisonInterface::TYPE constants
	 * @param mixed  $operand2
	 */
	function __construct($operand1, $operator, $operand2 = NULL) {
		$this->operand1 = $operand1;
		$this->operand2 = $operand2;
		$this->operator = $operator;
	}

	/**
	 * Gets the first operand
	 *
	 * @return mixed
	 */
	public function getProperty() {
		return $this->operand1;
	}

	/**
	 * Gets the operator
	 *
	 * @return string one of the TYPE constants
	 */
	public function getOperator() {
		return $this->operator;
	}

	/**
	 * Gets the second operand
	 *
	 * @return mixed
	 */
	public function getValue() {
		return $this->operand2;
	}

} 