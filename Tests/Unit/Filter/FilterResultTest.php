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
use Cundd\PersistentObjectStore\Driver\Driver;
use Cundd\PersistentObjectStore\Filter\Comparison;
use Cundd\PersistentObjectStore\Filter\ComparisonInterface;
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
		/** @var \Cundd\PersistentObjectStore\DataAccess\Coordinator $coordinator */
		$coordinator = $this->getDiContainer()->get('\Cundd\PersistentObjectStore\DataAccess\Coordinator');

		$this->filter = new Filter();

//		$database = $coordinator->getDataByDatabase('contacts');
//		$this->filter->addComparison(new Comparison('email', ComparisonInterface::TYPE_CONTAINS, '@cundd.net'));

		$database = $coordinator->getDataByDatabase('congress_members');
		$this->filter->addComparison(new Comparison('description', ComparisonInterface::TYPE_EQUAL_TO, 'Representative for Hawaii\'s 1st congressional district'));
		$this->fixture = $this->filter->filterCollection($database);
	}

	protected function tearDown() {
		unset($this->filter);
		unset($this->fixture);
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
}
 