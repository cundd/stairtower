<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 21.09.14
 * Time: 22:21
 */

namespace Cundd\PersistentObjectStore;

use Cundd\PersistentObjectStore\Domain\Model\Database;
use Cundd\PersistentObjectStore\Domain\Model\DocumentInterface;
use Cundd\PersistentObjectStore\Filter\Comparison\ComparisonInterface;


/**
 * Tests case that shows and proofs the usage of the database
 *
 * @package Cundd\PersistentObjectStore
 */
class ExampleTest extends AbstractDataBasedCase {
	/**
	 * @var \Cundd\PersistentObjectStore\DataAccess\Coordinator
	 */
	protected $fixture;


	protected function setUp() {
//		$this->setUpXhprof();

		$this->checkPersonFile();
		$this->fixture = $this->getDiContainer()->get('\Cundd\PersistentObjectStore\DataAccess\Coordinator');
	}

	/**
	 * @test
	 */
	public function exampleTest() {
		$startTime = microtime(TRUE);

		/** @var DocumentInterface $currentObject */

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
		$filterResult = $database->filter(new Filter\Comparison\PropertyComparison('eyeColor', ComparisonInterface::TYPE_EQUAL_TO, 'blue'));

		$currentObject = $filterResult->current();
		$this->assertNotNull($currentObject);

		$this->assertSame('robertgonzalez@cundd.net', $currentObject->valueForKey('email'));
		$this->assertSame('Robert Gonzalez', $currentObject->valueForKey('name'));
		$this->assertContains('blue', $currentObject->valueForKey('eyeColor'));


		// Ok, that's a guy... lets look for a girl
		$filterResult = $database->filter(
			new Filter\Comparison\LogicalComparison(ComparisonInterface::TYPE_AND,
				array(
					new Filter\Comparison\PropertyComparison('eyeColor', ComparisonInterface::TYPE_EQUAL_TO, 'blue'),
					new Filter\Comparison\PropertyComparison('gender', ComparisonInterface::TYPE_EQUAL_TO, 'female'),
				)
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

		$endTime = microtime(TRUE);
		printf('All this took us %0.6f seconds' . PHP_EOL, $endTime - $startTime);

		// Let's see how many people in the database have blue eyes
		$filterResult = $database->filter(new Filter\Comparison\PropertyComparison('eyeColor', ComparisonInterface::TYPE_EQUAL_TO, 'blue'));
		$blueEyes     = $filterResult->count();
		$this->assertSame(1684, $blueEyes);

		$endTime = microtime(TRUE);
		printf('All this took us %0.6f seconds' . PHP_EOL, $endTime - $startTime);


		// Let's see how many people in the database have brown eyes
		$filterResult = $database->filter(new Filter\Comparison\PropertyComparison('eyeColor', ComparisonInterface::TYPE_EQUAL_TO, 'brown'));
		$brownEyes    = $filterResult->count();
		$this->assertSame(1601, $brownEyes);

		$endTime = microtime(TRUE);
		printf('All this took us %0.6f seconds' . PHP_EOL, $endTime - $startTime);


		// Let's see how many people in the database have brown or blue eyes
		$filterResult  = $database->filter(new Filter\Comparison\PropertyComparison('eyeColor', ComparisonInterface::TYPE_IN, array('blue', 'brown')));
		$blueBrownEyes = $filterResult->count();
		$this->assertSame($blueEyes + $brownEyes, $blueBrownEyes);

		$endTime = microtime(TRUE);
		printf('All this took us %0.6f seconds' . PHP_EOL, $endTime - $startTime);


		$filterResult = $database->filter(
			new Filter\Comparison\LogicalComparison(ComparisonInterface::TYPE_OR,
				new Filter\Comparison\LogicalComparison(ComparisonInterface::TYPE_AND,
					array(
						new Filter\Comparison\PropertyComparison('eyeColor', ComparisonInterface::TYPE_EQUAL_TO, 'brown'),
						new Filter\Comparison\PropertyComparison('gender', ComparisonInterface::TYPE_EQUAL_TO, 'male'),
					)
				),
				new Filter\Comparison\LogicalComparison(ComparisonInterface::TYPE_AND,
					array(
						new Filter\Comparison\PropertyComparison('eyeColor', ComparisonInterface::TYPE_EQUAL_TO, 'blue'),
						new Filter\Comparison\PropertyComparison('gender', ComparisonInterface::TYPE_EQUAL_TO, 'female'),
					)
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

		$endTime = microtime(TRUE);
		printf('All this took us %0.6f seconds' . PHP_EOL, $endTime - $startTime);
	}
} 