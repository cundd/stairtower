<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 31.08.14
 * Time: 16:13
 */

namespace Cundd\PersistentObjectStore\DataAccess;

use Cundd\PersistentObjectStore\AbstractDataBasedCase;
use Cundd\PersistentObjectStore\Core\ArrayException\IndexOutOfRangeException;
use Cundd\PersistentObjectStore\Domain\Model\Database;
use Cundd\PersistentObjectStore\Domain\Model\DataInterface;
use Cundd\PersistentObjectStore\Driver\Driver;
use Cundd\PersistentObjectStore\Filter\Comparison\PropertyComparison;
use Cundd\PersistentObjectStore\Filter\Comparison\ComparisonInterface;
use Cundd\PersistentObjectStore\Filter\Filter;
use Cundd\PersistentObjectStore\Utility\DebugUtility;
use DI\ContainerBuilder;
use PHPUnit_Framework_TestCase;

/**
 * Test for Cundd\PersistentObjectStore\Filter\FilterResult
 *
 * @package Cundd\PersistentObjectStore\Filter
 */
class FilterResultTest extends AbstractDataBasedCase {
	/**
	 * @var \Cundd\PersistentObjectStore\Filter\FilterResult
	 */
	protected $fixture;

	/**
	 * @var Filter
	 */
	protected $filter;

	protected function setUp() {
		$this->checkCongressMemberFile();

		$this->setUpXhprof();

		/** @var \Cundd\PersistentObjectStore\DataAccess\Coordinator $coordinator */
		$coordinator = $this->getDiContainer()->get('\Cundd\PersistentObjectStore\DataAccess\Coordinator');

		$this->filter = new Filter();

//		$database = $coordinator->getDataByDatabase('contacts');
//		$this->filter->addComparison(new Comparison('email', ComparisonInterface::TYPE_CONTAINS, '@cundd.net'));

		$database = $coordinator->getDataByDatabase('congress_members');
		$this->filter->addComparison(new PropertyComparison('description', ComparisonInterface::TYPE_EQUAL_TO, 'Representative for Hawaii\'s 1st congressional district'));
		$this->fixture = $this->filter->filterCollection($database);
	}

	protected function tearDown() {
		unset($this->filter);
		unset($this->fixture);

		$this->tearDownXhprof();
	}

	/**
	 * @test
	 */
	public function currentTest() {
		$currentObject = $this->fixture->current();
		$this->assertNotNull($currentObject);

		$this->assertNotNull($this->fixture->current());
		$this->assertSame($currentObject, $this->fixture->current());
		$this->fixture->next();
		$this->assertNotNull($this->fixture->current());

		$this->assertSame('Neil', $this->fixture->current()->valueForKeyPath('person.firstname'));
	}

	/**
	 * @test
	 */
	public function nextTest() {
		$this->fixture->next();
		$this->assertNotNull($this->fixture->current());
		$this->assertSame('Neil', $this->fixture->current()->valueForKeyPath('person.firstname'));
	}

	/**
	 * @test
	 */
	public function countTest() {
		$this->assertEquals(60, $this->fixture->count());
		$this->assertNotNull($this->fixture->current());

		iterator_to_array($this->fixture);
		$this->assertEquals(60, $this->fixture->count());
	}

	/**
	 * @test
	 * @expectedException \Cundd\PersistentObjectStore\Core\ArrayException\IndexOutOfRangeException
	 */
	public function iterateAndGetCurrentShouldThrowAnException() {
		$this->assertEquals(60, $this->fixture->count());
		$this->assertNotNull($this->fixture->current());

		iterator_to_array($this->fixture);
		$this->assertEquals(60, $this->fixture->count());
		$this->assertNotNull($this->fixture->current());
	}

	/**
	 * @test
	 */
	public function doALotOfThings() {
		$exception = NULL;
		$this->assertEquals(60, $this->fixture->count());
		$this->assertNotNull($this->fixture->current());

		iterator_to_array($this->fixture);
		$this->assertEquals(60, $this->fixture->count());

		try {
			$this->fixture->rewind();
			$this->fixture->next();
			iterator_to_array($this->fixture);
			$this->fixture->rewind();

			$i = 0;
			while (++$i < $this->fixture->count()) {
				$this->fixture->next();
			}
		} catch (\Exception $exception) {
			echo $exception;
		}
		$this->assertNull($exception);
	}

	/**
	 * A test that should validate the behavior of data object references in a database
	 *
	 * @test
	 */
	public function objectLiveCycleTest() {
		/** @var \Cundd\PersistentObjectStore\DataAccess\Coordinator $coordinator */
		$coordinator = $this->getDiContainer()->get('\Cundd\PersistentObjectStore\DataAccess\Coordinator');

		$newFilter = new Filter();

//		$database = $coordinator->getDataByDatabase('contacts');
//		$this->filter->addComparison(new Comparison('email', ComparisonInterface::TYPE_CONTAINS, '@cundd.net'));

		$database = $coordinator->getDataByDatabase('congress_members');
//		$newFilter->addComparison(new PropertyComparison('description', ComparisonInterface::TYPE_EQUAL_TO, 'Representative for Wisconsin\'s 2nd congressional district'));
		$newFilter->addComparison(new PropertyComparison('description', ComparisonInterface::TYPE_EQUAL_TO, 'Representative for Hawaii\'s 1st congressional district'));
		$newFilterResult = $newFilter->filterCollection($database);

		/** @var DataInterface $memberFromNewFilter */
		$memberFromNewFilter = $newFilterResult->current();

		/** @var DataInterface $memberFromFixture */
		$memberFromFixture = $this->fixture->current();

		$this->assertEquals($memberFromNewFilter, $memberFromFixture);

		$movie = 'Star Wars';
		$key = 'favorite_movie';

		$memberFromNewFilter->setValueForKey($movie, $key);
		$this->assertEquals($memberFromNewFilter, $memberFromFixture);
		$this->assertEquals(spl_object_hash($memberFromNewFilter), spl_object_hash($memberFromFixture));
		$this->assertEquals($movie, $memberFromNewFilter->valueForKey($key));
		$this->assertEquals($movie, $memberFromFixture->valueForKey($key));

	}
}
 