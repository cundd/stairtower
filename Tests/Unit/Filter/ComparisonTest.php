<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 14.09.14
 * Time: 16:29
 */

namespace Cundd\PersistentObjectStore\Filter;
use Cundd\PersistentObjectStore\AbstractCase;
use Cundd\PersistentObjectStore\Domain\Model\Data;
use Cundd\PersistentObjectStore\Filter\Comparison\PropertyComparisonInterface;
use Cundd\PersistentObjectStore\Filter\Comparison\PropertyComparison;
use Cundd\PersistentObjectStore\Filter\Comparison\LogicalComparison;
use Cundd\PersistentObjectStore\KeyValueCodingInterface;
use Cundd\PersistentObjectStore\Utility\DebugUtility;
use Cundd\PersistentObjectStore\Utility\ObjectUtility;
use stdClass;


/**
 * Tests for different comparisons
 *
 * @package Cundd\PersistentObjectStore\Filter
 */
class ComparisonTest extends AbstractCase {
	/**
	 * @var PropertyComparisonInterface
	 */
	protected $fixture;

	protected function setUp() {
		$this->setUpXhprof();
	}

	protected function tearDown() {
		unset($this->fixture);
		$this->tearDownXhprof();
	}

	/**
	 * @test
	 */
	public function equalToTest() {
		$propertyKey = 'name';
		$testValue = 'Daniel';
		$testObject = new stdClass();
		$testObject->name = 'Daniel';

		$this->fixture = new PropertyComparison($propertyKey, PropertyComparisonInterface::TYPE_EQUAL_TO, $testValue);
		$this->assertTrue($this->fixture->perform($testObject));

		$testObject->name = 'Bob';
		$this->assertFalse($this->fixture->perform($testObject));
	}

	/**
	 * @test
	 */
	public function notEqualToTest() {
		$propertyKey = 'name';
		$testValue = 'Bob';
		$testObject = new stdClass();
		$testObject->name = 'Daniel';
		$this->fixture = new PropertyComparison($propertyKey, PropertyComparisonInterface::TYPE_NOT_EQUAL_TO, $testValue);
		$this->assertTrue($this->fixture->perform($testObject));

		$testObject->name = 'Bob';
		$this->assertFalse($this->fixture->perform($testObject));
	}

	/**
	 * @test
	 */
	public function lessThanTest() {
		$propertyKey = 'number';
		$testValue = 2;
		$testObject = new stdClass();
		$testObject->number = 1;
		$this->fixture = new PropertyComparison($propertyKey, PropertyComparisonInterface::TYPE_LESS_THAN, $testValue);
		$this->assertTrue($this->fixture->perform($testObject));

		$testObject->number = 2;
		$this->assertFalse($this->fixture->perform($testObject));
	}

	/**
	 * @test
	 */
	public function lessThanOrEqualToTest() {
		$propertyKey = 'number';
		$testValue = 2;
		$testObject = new stdClass();
		$testObject->number = 1;
		$this->fixture = new PropertyComparison($propertyKey, PropertyComparisonInterface::TYPE_LESS_THAN_OR_EQUAL_TO, $testValue);
		$this->assertTrue($this->fixture->perform($testObject));

		$testObject->number = 2;
		$this->assertTrue($this->fixture->perform($testObject));

		$testObject->number = 3;
		$this->assertFalse($this->fixture->perform($testObject));
	}

	/**
	 * @test
	 */
	public function greaterThanTest() {
		$propertyKey = 'number';
		$testValue = 2;
		$testObject = new stdClass();
		$testObject->number = 10;
		$this->fixture = new PropertyComparison($propertyKey, PropertyComparisonInterface::TYPE_GREATER_THAN, $testValue);
		$this->assertTrue($this->fixture->perform($testObject));

		$testObject->number = 2;
		$this->assertFalse($this->fixture->perform($testObject));

		$testObject->number = 1;
		$this->assertFalse($this->fixture->perform($testObject));
	}

	/**
	 * @test
	 */
	public function greaterThanOrEqualToTest() {
		$propertyKey = 'number';
		$testValue = 2;
		$testObject = new stdClass();
		$testObject->number = 10;
		$this->fixture = new PropertyComparison($propertyKey, PropertyComparisonInterface::TYPE_GREATER_THAN_OR_EQUAL_TO, $testValue);
		$this->assertTrue($this->fixture->perform($testObject));

		$testObject->number = 2;
		$this->assertTrue($this->fixture->perform($testObject));

		$testObject->number = 1;
		$this->assertFalse($this->fixture->perform($testObject));
	}

	/**
	 * @test
	 */
	public function likeTest() {
		$propertyKey = 'name';
		$testValue = 'Daniel';
		$testObject = new stdClass();
		$testObject->name = 'Daniel';
		$this->fixture = new PropertyComparison($propertyKey, PropertyComparisonInterface::TYPE_LIKE, $testValue);
		$this->assertTrue($this->fixture->perform($testObject));
	}

	/**
	 * @test
	 */
	public function containsTest() {
		$propertyKey = 'name';
		$testValue = 'Daniel';
		$testObject = new stdClass();
		$testObject->name = 'Daniel';
		$this->fixture = new PropertyComparison($propertyKey, PropertyComparisonInterface::TYPE_CONTAINS, $testValue);
		$this->assertTrue($this->fixture->perform($testObject));
	}

	/**
	 * @test
	 */
	public function inTest() {
		$propertyKey = 'name';
		$testValue = 'Daniel';
		$testObject = new stdClass();
		$testObject->name = 'Daniel';
		$this->fixture = new PropertyComparison($propertyKey, PropertyComparisonInterface::TYPE_IN, $testValue);
		$this->assertTrue($this->fixture->perform($testObject));
	}

	/**
	 * @test
	 */
	public function isNullTest() {
		$propertyKey = 'name';
		$testValue = 'Daniel';
		$testObject = new stdClass();
		$testObject->name = NULL;
		$this->fixture = new PropertyComparison($propertyKey, PropertyComparisonInterface::TYPE_IS_NULL, $testValue);
		$this->assertTrue($this->fixture->perform($testObject));
	}

	/**
	 * @test
	 */
	public function isEmptyTest() {
		$propertyKey = 'name';
		$testValue = 'Daniel';
		$testObject = new stdClass();
		$testObject->name = '';
		$this->fixture = new PropertyComparison($propertyKey, PropertyComparisonInterface::TYPE_IS_EMPTY, $testValue);
		$this->assertTrue($this->fixture->perform($testObject));

		$i = 0;
		while ($i++ < 10000) {
			$this->assertTrue($this->fixture->perform($testObject));
		}

		$testObject->name = array();
		$this->assertTrue($this->fixture->perform($testObject));

		$testObject->name = NULL;
		$this->assertTrue($this->fixture->perform($testObject));
	}

	/**
	 * @test
	 */
	public function bbTest(){
		$testValue = new Data();

		$i = 0;
		while ($i++ < 1000000) {
			if ($testValue instanceof KeyValueCodingInterface) {

			}
		}
	}

	/**
	 * @test
	 */
	public function andTest() {
		$testObject = new stdClass();
		$testObject->name = 'Yvonne';
		$testObject->status = 'Girlfriend';

		$this->fixture = new LogicalComparison(
			PropertyComparisonInterface::TYPE_AND,
			new PropertyComparison('name', PropertyComparisonInterface::TYPE_EQUAL_TO, 'Yvonne'),
			new PropertyComparison('status', PropertyComparisonInterface::TYPE_EQUAL_TO, 'Girlfriend')
		);
		$i = 0;
		while ($i++ < 10000) {
			$this->assertTrue($this->fixture->perform($testObject));
		}


		$testObject->status = 'Ex';
		$this->assertFalse($this->fixture->perform($testObject));
	}

	/**
	 * @test
	 */
	public function orTest() {
		$testObject = new stdClass();
		$testObject->name = 'Yvonne';
		$testObject->status = 'Girlfriend';

		$this->fixture = new LogicalComparison(
			PropertyComparisonInterface::TYPE_OR,
			new PropertyComparison('name', PropertyComparisonInterface::TYPE_EQUAL_TO, 'Yvonne'),
			new PropertyComparison('status', PropertyComparisonInterface::TYPE_EQUAL_TO, 'Girlfriend')
		);

		$i = 0;
		while ($i++ < 100000) {
			$this->assertTrue($this->fixture->perform($testObject));
		}

		$this->assertTrue($this->fixture->perform($testObject));

		$testObject->status = 'Ex';
		$this->assertTrue($this->fixture->perform($testObject));

		$testObject->name = 'Bob';
		$testObject->status = 'Son';
		$this->assertFalse($this->fixture->perform($testObject));
	}
}
 