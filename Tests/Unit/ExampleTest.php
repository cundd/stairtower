<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Tests\Unit;

use Cundd\Stairtower\DataAccess\Coordinator;
use Cundd\Stairtower\Domain\Model\Database;
use Cundd\Stairtower\Filter\Comparison\ComparisonInterface;
use Cundd\Stairtower\Filter\Comparison\LogicalComparison;
use Cundd\Stairtower\Filter\Comparison\PropertyComparison;


/**
 * Tests case that shows and proofs the usage of the database
 */
class ExampleTest extends AbstractDataBasedCase
{
    /**
     * @var \Cundd\Stairtower\DataAccess\Coordinator
     */
    protected $fixture;

    /**
     * @test
     */
    public function exampleTest()
    {
        // Load a database called 'people'
        /** @var Database $database */
        $database = $this->fixture->getDatabase('people');

        // Count the number of people in the database
        $this->assertGreaterThan(0, $database->count());

        // Get the first person from the database
        $currentObject = $database->current();
        $this->assertNotNull($currentObject);

        // Lets check if this is the person we look for
        $this->assertSame('spm@cundd.net', $currentObject->valueForKey('email'));
        $this->assertSame('Daniel Corn', $currentObject->valueForKey('name'));
        $this->assertContains('developer', $currentObject->valueForKey('tags'));
        $this->assertContains('brown', $currentObject->valueForKey('eyeColor'));

        // Brown eyes are ok, but lets search for someone with blue eyes
        $filterResult = $database->filter(
            new PropertyComparison(
                'eyeColor',
                ComparisonInterface::TYPE_EQUAL_TO, 'blue'
            )
        );

        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertSame('robertgonzalez@cundd.net', $currentObject->valueForKey('email'));
        $this->assertSame('Robert Gonzalez', $currentObject->valueForKey('name'));
        $this->assertContains('blue', $currentObject->valueForKey('eyeColor'));


        // Ok, that's a guy... lets look for a girl
        $filterResult = $database->filter(
            new LogicalComparison(
                ComparisonInterface::TYPE_AND,
                new PropertyComparison('eyeColor', ComparisonInterface::TYPE_EQUAL_TO, 'blue'),
                new PropertyComparison('gender', ComparisonInterface::TYPE_EQUAL_TO, 'female')
            )
        );

        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertSame('angelaroberts@cundd.net', $currentObject->valueForKey('email'));
        $this->assertSame('Angela Roberts', $currentObject->valueForKey('name'));
        $this->assertContains('blue', $currentObject->valueForKey('eyeColor'));


        // It's getting hotter! But is there another one?
        $filterResult->next();
        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertSame('frankiehorn@cundd.net', $currentObject->valueForKey('email'));
        $this->assertSame('Frankie Horn', $currentObject->valueForKey('name'));
        $this->assertContains('blue', $currentObject->valueForKey('eyeColor'));


        // Let's see how many people in the database have blue eyes
        $filterResult = $database->filter(
            new PropertyComparison(
                'eyeColor',
                ComparisonInterface::TYPE_EQUAL_TO, 'blue'
            )
        );
        $blueEyes = $filterResult->count();
        $this->assertSame(1684, $blueEyes);


        // Let's see how many people in the database have brown eyes
        $filterResult = $database->filter(
            new PropertyComparison(
                'eyeColor',
                ComparisonInterface::TYPE_EQUAL_TO, 'brown'
            )
        );
        $brownEyes = $filterResult->count();
        $this->assertSame(1601, $brownEyes);


        // Let's see how many people in the database have brown or blue eyes
        $filterResult = $database->filter(
            new PropertyComparison(
                'eyeColor',
                ComparisonInterface::TYPE_IN, ['blue', 'brown']
            )
        );
        $blueBrownEyes = $filterResult->count();
        $this->assertSame($blueEyes + $brownEyes, $blueBrownEyes);


        $filterResult = $database->filter(
            new LogicalComparison(
                ComparisonInterface::TYPE_OR,
                new LogicalComparison(
                    ComparisonInterface::TYPE_AND,
                    new PropertyComparison('eyeColor', ComparisonInterface::TYPE_EQUAL_TO, 'brown'),
                    new PropertyComparison('gender', ComparisonInterface::TYPE_EQUAL_TO, 'male')
                ),
                new LogicalComparison(
                    ComparisonInterface::TYPE_AND,
                    new PropertyComparison('eyeColor', ComparisonInterface::TYPE_EQUAL_TO, 'blue'),
                    new PropertyComparison('gender', ComparisonInterface::TYPE_EQUAL_TO, 'female')
                )
            )
        );

        $currentObject = $filterResult->current();
        $this->assertEquals('Daniel Corn', $currentObject->valueForKey('name'));
        $this->assertEquals('male', $currentObject->valueForKey('gender'));
        $this->assertEquals('brown', $currentObject->valueForKey('eyeColor'));

        $filterResult->next();
        $currentObject = $filterResult->current();
        $this->assertEquals('Angela Roberts', $currentObject->valueForKey('name'));
        $this->assertEquals('female', $currentObject->valueForKey('gender'));
        $this->assertEquals('blue', $currentObject->valueForKey('eyeColor'));

        $filterResult->next();
        $currentObject = $filterResult->current();
        $this->assertEquals('Frankie Horn', $currentObject->valueForKey('name'));
        $this->assertEquals('female', $currentObject->valueForKey('gender'));
        $this->assertEquals('blue', $currentObject->valueForKey('eyeColor'));
    }

    protected function setUp()
    {
        parent::setUp();
        $this->checkPersonFile();
        $this->fixture = $this->getDiContainer()->get(Coordinator::class);
    }
} 