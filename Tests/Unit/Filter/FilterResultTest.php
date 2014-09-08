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

//	protected function setUp() {
//		echo __LINE__ . PHP_EOL;
//
//
//		$builder = new ContainerBuilder();
//		$builder->setDefinitionCache(new \Doctrine\Common\Cache\ArrayCache());
//		$builder->addDefinitions(__DIR__ . '/../../../Classes/Configuration/dependencyInjectionConfiguration.php');
//		$diContainer = $builder->build();
//
//		/** @var \Cundd\PersistentObjectStore\DataAccess\Coordinator $coordinator */
//		$coordinator = $diContainer->get('\Cundd\PersistentObjectStore\DataAccess\Coordinator');
////		$coordinator = $this->getDiContainer()->get('\Cundd\PersistentObjectStore\DataAccess\Coordinator');
//		$database = $coordinator->getDataByDatabase('congress_members');
//
//		echo __LINE__ . PHP_EOL;
//
//		$this->filter = new Filter();
//		$this->filter->addComparison(new Comparison('description', ComparisonInterface::TYPE_EQUAL_TO, 'Representative for Hawaii\'s 1st congressional district'));
//		$this->fixture = $this->filter->filterCollection($database);
//
//		echo __LINE__ . PHP_EOL;
//
//	}

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

		DebugUtility::var_dump($currentObject);
		echo PHP_EOL;
		echo PHP_EOL;
		$this->assertNotNull($this->fixture->current());
		$this->assertSame($currentObject, $this->fixture->current());
		$this->fixture->next();
		$this->assertNotNull($this->fixture->current());

		$this->assertSame('Neil', $this->fixture->current()->valueForKeyPath('person.firstname'));

		echo $this->formatBytes(memory_get_peak_usage(TRUE)) . PHP_EOL;
		echo $this->formatBytes(memory_get_usage(TRUE)) . PHP_EOL;
	}


	/**
	 * @test
	 */
	public function countTest() {
		$this->assertEquals(60, $this->fixture->count());
		$this->assertNotNull($this->fixture->current());

		iterator_to_array($this->fixture);
		$this->assertEquals(60, $this->fixture->count());

		echo $this->formatBytes(memory_get_peak_usage(TRUE)) . PHP_EOL;
		echo $this->formatBytes(memory_get_usage(TRUE)) . PHP_EOL;
	}

	/**
	 * @test
	 * @expected \Cundd\PersistentObjectStore\Core\ArrayException\IndexOutOfRangeException
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
	public function nextTest() {
		$this->fixture->next();
		$this->assertNotNull($this->fixture->current());
		$this->assertSame('Neil', $this->fixture->current()->valueForKeyPath('person.firstname'));

		echo $this->formatBytes(memory_get_peak_usage(TRUE)) . PHP_EOL;
		echo $this->formatBytes(memory_get_usage(TRUE)) . PHP_EOL;
	}
}
 