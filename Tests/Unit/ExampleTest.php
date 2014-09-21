<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 21.09.14
 * Time: 22:21
 */

namespace Cundd\PersistentObjectStore;
use Cundd\PersistentObjectStore\Domain\Model\Database;
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
		parent::setUp();

		$this->checkPersonFile();
		$this->fixture = $this->getDiContainer()->get('\Cundd\PersistentObjectStore\DataAccess\Coordinator');
	}

	/**
	 * @test
	 */
	public function exampleTest() {
		$startTime = microtime(TRUE);

		// Load a database called 'people'
		/** @var Database $database */
		$database = $this->fixture->getDataByDatabase('people');

		// Count the number of people in the database
		$this->assertGreaterThan(0, $database->count());

		// Get the first person from the database
		$currentObject = $database->current();
		$this->assertNotNull($currentObject);

		// Lets check if this is the person we look for
		$this->assertSame('spm@cundd.net', $currentObject->valueForKeyPath('email'));
		$this->assertSame('Daniel Corn', $currentObject->valueForKeyPath('name'));
		$this->assertContains('developer', $currentObject->valueForKeyPath('tags'));
		$this->assertContains('brown', $currentObject->valueForKeyPath('eyeColor'));

		// Brown eyes are ok, but lets search for someone with blue eyes
		$filterResult = $database->filter(new Filter\Comparison\PropertyComparison('eyeColor', ComparisonInterface::TYPE_EQUAL_TO, 'blue'));

		$currentObject = $filterResult->current();
		$this->assertNotNull($currentObject);

		$this->assertSame('robertgonzalez@cundd.net', $currentObject->valueForKeyPath('email'));
		$this->assertSame('Robert Gonzalez', $currentObject->valueForKeyPath('name'));
		$this->assertContains('blue', $currentObject->valueForKeyPath('eyeColor'));


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

		$this->assertSame('angelaroberts@cundd.net', $currentObject->valueForKeyPath('email'));
		$this->assertSame('Angela Roberts', $currentObject->valueForKeyPath('name'));
		$this->assertContains('blue', $currentObject->valueForKeyPath('eyeColor'));


		// It's getting hotter! But is there another one?
		$filterResult->next();
		$currentObject = $filterResult->current();
		$this->assertNotNull($currentObject);

		$this->assertSame('frankiehorn@cundd.net', $currentObject->valueForKeyPath('email'));
		$this->assertSame('Frankie Horn', $currentObject->valueForKeyPath('name'));
		$this->assertContains('blue', $currentObject->valueForKeyPath('eyeColor'));

		$endTime = microtime(TRUE);
		printf('All this took us %0.6f seconds', $endTime - $startTime);
	}
} 