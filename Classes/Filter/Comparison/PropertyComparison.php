<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 01.09.14
 * Time: 21:45
 */

namespace Cundd\PersistentObjectStore\Filter\Comparison;
use Cundd\PersistentObjectStore\Domain\Model\DataInterface;
use Cundd\PersistentObjectStore\Filter\Comparison\PropertyComparisonInterface;
use Cundd\PersistentObjectStore\Filter\Exception\InvalidComparisonException;
use Cundd\PersistentObjectStore\KeyValueCodingInterface;
use Cundd\PersistentObjectStore\Utility\DebugUtility;
use Cundd\PersistentObjectStore\Utility\ObjectUtility;


/**
 * Property comparison implementation
 *
 * @package Cundd\PersistentObjectStore\Filter
 */
class PropertyComparison implements PropertyComparisonInterface {
	/**
	 * Property key of the test value that will be compared against the comparison value
	 *
	 * @var string
	 */
	protected $property;

	/**
	 * Value to test against
	 *
	 * @var mixed
	 */
	protected $value;

	/**
	 * Type of the comparison from the comparison value against the given test data's property
	 *
	 * @var string One of the ComparisonInterface::TYPE constants
	 */
	protected $operator;

	/**
	 * Creates a new comparison
	 *
	 * @param string $property Property key of the test value that will be compared against the comparison value
	 * @param string $operator One of the ComparisonInterface::TYPE constants
	 * @param mixed  $value Value to test against
	 */
	function __construct($property, $operator, $value = NULL) {
		$this->property = $property;
		$this->value = $value;
		$this->operator = $operator;
	}

	/**
	 * Returns the property key of the test value that will be compared against the comparison value
	 *
	 * @return mixed
	 */
	public function getProperty() {
		return $this->property;
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
	 * Returns the value to test against
	 *
	 * @return mixed
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * Performs the comparison against the given test value
	 *
	 * @param mixed $testValue
	 * @return bool
	 */
	public function perform($testValue) {
		if ($testValue instanceof KeyValueCodingInterface) {
//			$testValue = $testValue->getData();
			$propertyValue = $testValue->valueForKeyPath($this->property);
//			$propertyValue = $testValue->valueForKeyPath($this->getProperty());
		} else {
			$propertyValue = ObjectUtility::valueForKeyPathOfObject($this->property, $testValue);
//			$propertyValue = ObjectUtility::valueForKeyPathOfObject($this->getProperty(), $testValue);
		}
		$operator = $this->getOperator();
		switch ($operator) {
			case PropertyComparisonInterface::TYPE_EQUAL_TO:
				return $propertyValue === $this->getValue();

			case PropertyComparisonInterface::TYPE_NOT_EQUAL_TO:
				return $propertyValue !== $this->getValue();

			case PropertyComparisonInterface::TYPE_LESS_THAN:
				return $propertyValue < $this->getValue();

			case PropertyComparisonInterface::TYPE_LESS_THAN_OR_EQUAL_TO:
				return $propertyValue <= $this->getValue();

			case PropertyComparisonInterface::TYPE_GREATER_THAN:
				return $propertyValue > $this->getValue();

			case PropertyComparisonInterface::TYPE_GREATER_THAN_OR_EQUAL_TO:
				return $propertyValue >= $this->getValue();

			case PropertyComparisonInterface::TYPE_LIKE:
				return $propertyValue === $this->getValue();

			case PropertyComparisonInterface::TYPE_CONTAINS:
				return $this->performContains($propertyValue, $this->getValue());

			case PropertyComparisonInterface::TYPE_IN:
				return $this->performContains($this->getValue(), $propertyValue);

			case PropertyComparisonInterface::TYPE_AND:
			case PropertyComparisonInterface::TYPE_OR:
				return $this->performLogical($testValue, $this->getOperator());

			case PropertyComparisonInterface::TYPE_IS_NULL:
				return is_null($propertyValue);

			case PropertyComparisonInterface::TYPE_IS_EMPTY:
				return !$propertyValue;
		}
		return FALSE;
	}

	/**
	 * Performs the given logical comparison
	 *
	 * @param mixed  $testValue
	 * @param string $operator
	 * @throws InvalidComparisonException if the given operator is neither ComparisonInterface::TYPE_AND nor ComparisonInterface::TYPE_OR
	 * @return bool
	 */
	protected function performLogical($testValue, $operator) {
		$expression1 = $this->property;
		$expression2 = $this->value;

		$result1 = (bool)($expression1 instanceof PropertyComparisonInterface ? $expression1->perform($testValue) : $expression1);

		if ($operator === PropertyComparisonInterface::TYPE_AND) {
			$result2 = (bool)($expression2 instanceof PropertyComparisonInterface ? $expression2->perform($testValue) : $expression2);
			return $result1 && $result2;
		} else if ($operator === PropertyComparisonInterface::TYPE_OR) {
			if ($result1) {
				return $result1;
			}
			return (bool)($expression2 instanceof PropertyComparisonInterface ? $expression2->perform($testValue) : $expression2);
		}
		throw new InvalidComparisonException(sprintf('Can not perform logical comparison with operator %s', $operator), 1410704637);
	}

	/**
	 * Perform a 'contains' comparison
	 *
	 * @param array|\Traversable $collection
	 * @param mixed              $search
	 * @return bool
	 */
	protected function performContains($collection, $search) {
		if ($collection instanceof \Traversable) {
			$collection = iterator_to_array($collection);
		}
		if (is_array($collection)) {
			return in_array($search, $collection);
		}
		if (is_string($collection)) {
			return strpos($collection, (string)$search) !== FALSE;
		}
		return FALSE;
	}


} 