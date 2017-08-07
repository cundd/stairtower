<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Filter;

use Cundd\PersistentObjectStore\AbstractCase;
use Cundd\PersistentObjectStore\Filter\Comparison\LogicalComparison;
use Cundd\PersistentObjectStore\Filter\Comparison\PropertyComparison;
use Cundd\PersistentObjectStore\Filter\Comparison\PropertyComparisonInterface;
use stdClass;


/**
 * Tests for different comparisons
 */
class ComparisonTest extends AbstractCase
{
    /**
     * @var PropertyComparisonInterface
     */
    protected $fixture;

    /**
     * @test
     */
    public function equalToTest()
    {
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
    public function notEqualToTest()
    {
        $propertyKey = 'name';
        $testValue = 'Bob';
        $testObject = new stdClass();
        $testObject->name = 'Daniel';
        $this->fixture = new PropertyComparison(
            $propertyKey, PropertyComparisonInterface::TYPE_NOT_EQUAL_TO,
            $testValue
        );
        $this->assertTrue($this->fixture->perform($testObject));

        $testObject->name = 'Bob';
        $this->assertFalse($this->fixture->perform($testObject));
    }

    /**
     * @test
     */
    public function lessThanTest()
    {
        $propertyKey = 'number';
        $testValue = 2;
        $testObject = new stdClass();
        $testObject->number = 1;
        $this->fixture = new PropertyComparison(
            $propertyKey, PropertyComparisonInterface::TYPE_LESS_THAN,
            $testValue
        );
        $this->assertTrue($this->fixture->perform($testObject));

        $testObject->number = 2;
        $this->assertFalse($this->fixture->perform($testObject));
    }

    /**
     * @test
     */
    public function lessThanOrEqualToTest()
    {
        $propertyKey = 'number';
        $testValue = 2;
        $testObject = new stdClass();
        $testObject->number = 1;
        $this->fixture = new PropertyComparison(
            $propertyKey,
            PropertyComparisonInterface::TYPE_LESS_THAN_OR_EQUAL_TO, $testValue
        );
        $this->assertTrue($this->fixture->perform($testObject));

        $testObject->number = 2;
        $this->assertTrue($this->fixture->perform($testObject));

        $testObject->number = 3;
        $this->assertFalse($this->fixture->perform($testObject));
    }

    /**
     * @test
     */
    public function greaterThanTest()
    {
        $propertyKey = 'number';
        $testValue = 2;
        $testObject = new stdClass();
        $testObject->number = 10;
        $this->fixture = new PropertyComparison(
            $propertyKey, PropertyComparisonInterface::TYPE_GREATER_THAN,
            $testValue
        );
        $this->assertTrue($this->fixture->perform($testObject));

        $testObject->number = 2;
        $this->assertFalse($this->fixture->perform($testObject));

        $testObject->number = 1;
        $this->assertFalse($this->fixture->perform($testObject));
    }

    /**
     * @test
     */
    public function greaterThanOrEqualToTest()
    {
        $propertyKey = 'number';
        $testValue = 2;
        $testObject = new stdClass();
        $testObject->number = 10;
        $this->fixture = new PropertyComparison(
            $propertyKey,
            PropertyComparisonInterface::TYPE_GREATER_THAN_OR_EQUAL_TO, $testValue
        );
        $this->assertTrue($this->fixture->perform($testObject));

        $testObject->number = 2;
        $this->assertTrue($this->fixture->perform($testObject));

        $testObject->number = 1;
        $this->assertFalse($this->fixture->perform($testObject));
    }

    /**
     * @test
     */
    public function likeTest()
    {
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
    public function containsTest()
    {
        $propertyKey = 'name';
        $testValue = 'Daniel';
        $testObject = new stdClass();
        $testObject->name = 'Daniel';
        $this->fixture = new PropertyComparison(
            $propertyKey, PropertyComparisonInterface::TYPE_CONTAINS,
            $testValue
        );
        $this->assertTrue($this->fixture->perform($testObject));
    }

    /**
     * @test
     */
    public function inTest()
    {
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
    public function isNullTest()
    {
        $propertyKey = 'name';
        $testValue = 'Daniel';
        $testObject = new stdClass();
        $testObject->name = null;
        $this->fixture = new PropertyComparison($propertyKey, PropertyComparisonInterface::TYPE_IS_NULL, $testValue);
        $this->assertTrue($this->fixture->perform($testObject));
    }

    /**
     * @test
     */
    public function isEmptyTest()
    {
        $propertyKey = 'name';
        $testValue = 'Daniel';
        $testObject = new stdClass();
        $testObject->name = '';
        $this->fixture = new PropertyComparison(
            $propertyKey, PropertyComparisonInterface::TYPE_IS_EMPTY,
            $testValue
        );
        $this->assertTrue($this->fixture->perform($testObject));

        $testObject->name = [];
        $this->assertTrue($this->fixture->perform($testObject));

        $testObject->name = null;
        $this->assertTrue($this->fixture->perform($testObject));
    }

    /**
     * @test
     */
    public function andTest()
    {
        $testObject = new stdClass();
        $testObject->name = 'Yvonne';
        $testObject->status = 'Girlfriend';

        $this->fixture = new LogicalComparison(
            PropertyComparisonInterface::TYPE_AND,
            new PropertyComparison('name', PropertyComparisonInterface::TYPE_EQUAL_TO, 'Yvonne'),
            new PropertyComparison('status', PropertyComparisonInterface::TYPE_EQUAL_TO, 'Girlfriend')
        );

        $testObject->status = 'Ex';
        $this->assertFalse($this->fixture->perform($testObject));
    }

    /**
     * @test
     */
    public function andFactoryTest()
    {
        $testObject = new stdClass();
        $testObject->name = 'Yvonne';
        $testObject->status = 'Girlfriend';

        $this->fixture = LogicalComparison::logicalAnd(
            new PropertyComparison('name', PropertyComparisonInterface::TYPE_EQUAL_TO, 'Yvonne'),
            new PropertyComparison('status', PropertyComparisonInterface::TYPE_EQUAL_TO, 'Girlfriend')
        );

        $testObject->status = 'Ex';
        $this->assertFalse($this->fixture->perform($testObject));
    }

    /**
     * @test
     */
    public function orTest()
    {
        $testObject = new stdClass();
        $testObject->name = 'Yvonne';
        $testObject->status = 'Girlfriend';

        $this->fixture = new LogicalComparison(
            PropertyComparisonInterface::TYPE_OR,
            new PropertyComparison('name', PropertyComparisonInterface::TYPE_EQUAL_TO, 'Yvonne'),
            new PropertyComparison('status', PropertyComparisonInterface::TYPE_EQUAL_TO, 'Girlfriend')
        );

        $this->assertTrue($this->fixture->perform($testObject));

        $testObject->status = 'Ex';
        $this->assertTrue($this->fixture->perform($testObject));

        $testObject->name = 'Bob';
        $testObject->status = 'Son';
        $this->assertFalse($this->fixture->perform($testObject));
    }

    /**
     * @test
     */
    public function orFactoryTest()
    {
        $testObject = new stdClass();
        $testObject->name = 'Yvonne';
        $testObject->status = 'Girlfriend';

        $this->fixture = LogicalComparison::logicalOr(
            new PropertyComparison('name', PropertyComparisonInterface::TYPE_EQUAL_TO, 'Yvonne'),
            new PropertyComparison('status', PropertyComparisonInterface::TYPE_EQUAL_TO, 'Girlfriend')
        );

        $this->assertTrue($this->fixture->perform($testObject));

        $testObject->status = 'Ex';
        $this->assertTrue($this->fixture->perform($testObject));

        $testObject->name = 'Bob';
        $testObject->status = 'Son';
        $this->assertFalse($this->fixture->perform($testObject));
    }

    protected function setUp()
    {
        $this->setUpXhprof();
    }

    protected function tearDown()
    {
        unset($this->fixture);
    }
}
 