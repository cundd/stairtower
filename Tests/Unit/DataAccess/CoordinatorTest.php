<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 31.08.14
 * Time: 16:13
 */

namespace Cundd\PersistentObjectStore\DataAccess;
use Cundd\PersistentObjectStore\AbstractDataBasedCase;
use Cundd\PersistentObjectStore\Domain\Model\Database;
use Cundd\PersistentObjectStore\Driver\Driver;
use Cundd\PersistentObjectStore\Filter\Comparison;
use Cundd\PersistentObjectStore\Filter\ComparisonInterface;
use Cundd\PersistentObjectStore\Filter\Filter;

/**
 * Test for Cundd\PersistentObjectStore\DataAccess\Coordinator
 *
 * @package Cundd\PersistentObjectStore\DataAccess
 */
class CoordinatorTest extends AbstractDataBasedCase {
	/**
	 * @var \Cundd\PersistentObjectStore\DataAccess\Coordinator
	 */
	protected $fixture;


	/**
	 * @test
	 */
	public function filterTestsCongressMembers() {
		/** @var Database $database */
		$database = $this->fixture->getDataByDatabase('congress_members');
		$this->assertEquals(4800, $database->count());


		$filter = new Filter();
		$filter->addComparison(new Comparison('description', ComparisonInterface::TYPE_EQUAL_TO, 'Representative for Hawaii\'s 1st congressional district'));
		$filterResult = $filter->filterCollection($database);

		$this->assertEquals(60, $filterResult->count());
		$this->assertNotNull($filterResult->current());
		$this->assertSame('Neil', $filterResult->current()->valueForKeyPath('person.firstname'));


//		$filter = new Filter();
//		$filter->addComparison(new Comparison('description', ComparisonInterface::TYPE_CONTAINS, 'New York'));
//		$filterResult = $filter->filterCollection($database);
//		$this->assertEquals(256, $filterResult->count());
//		$this->assertNotNull($filterResult->current());
//		$this->assertSame('Gary', $filterResult->current()->valueForKeyPath('person.firstname'));

		echo $this->formatBytes(memory_get_peak_usage(TRUE)) . PHP_EOL;
		echo $this->formatBytes(memory_get_usage(TRUE)) . PHP_EOL;
	}

	/**
	 * @test
	 */
	public function filterTests() {
		/** @var Database $database */
		$database = $this->fixture->getDataByDatabase('contacts');

		$filter = new Filter();
		$filter->addComparison(new Comparison('email', ComparisonInterface::TYPE_EQUAL_TO, 'spm@cundd.net'));
		$filterResult = $filter->filterCollection($database);
		$this->assertEquals(1, $filterResult->count());
		$this->assertNotNull($filterResult->current());
		$this->assertSame('spm@cundd.net', $filterResult->current()->valueForKey('email'));


		$filter = new Filter();
		$filter->addComparison(new Comparison('email', ComparisonInterface::TYPE_CONTAINS, '@cundd.net'));
		$filterResult = $filter->filterCollection($database);
		$this->assertEquals(2, $filterResult->count());
		$this->assertNotNull($filterResult->current());
		$this->assertSame('info@cundd.net', $filterResult->current()->valueForKey('email'));
	}

	/**
	 * @test
	 */
	public function doctrineQueryBuilderTests() {
		return;
		$queryBuilder = new \Doctrine\DBAL\Query\QueryBuilder(new \Cundd\PersistentObjectStore\Connection(array(), new Driver()));
//		$queryBuilder
//			->from('contacts', 'contacts')
//		;
//		$database = $this->fixture->getDataByQuery($queryBuilder);
//		$this->assertSame($this->getAllTestData(), $this->databaseToDataArray($database));
//
//
//		$queryBuilder
//			->select('id', 'name')
//			->from('contacts', 'contacts')
//		;
//		$database = $this->fixture->getDataByQuery($queryBuilder);
//		$this->assertSame($this->getAllTestData(), $this->databaseToDataArray($database));


		$queryBuilder
			->select('id', 'name')
			->from('contacts', 'users')
			->where('email = ?')
		->andWhere('email = ?')
		->orWhere('email = ?')
		->andWhere('email = ?')
		->orWhere('email = ?')
			->setParameter(0, 'spm@cundd.net')
		;
		$database = $this->fixture->getDataByQuery($queryBuilder);
		$this->assertSame($this->getAllTestData(), $this->databaseToDataArray($database));



		return;
		$queryBuilder->select('c')
			->innerJoin('c.lastName', 'contacts', 'ON', $queryBuilder->expr()->eq('p.lastName', ':lastName'))
			->where('c.email = :email');

		$queryBuilder->setParameters(array(
			'email' => 'spm@cundd.net',
			'lastName' => 'Jobs',
		));

		$database = $this->fixture->getDataByQuery($queryBuilder);
		$this->assertSame($this->getAllTestData(), $this->databaseToDataArray($database));
	}
}
 