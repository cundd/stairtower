<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 31.08.14
 * Time: 16:13
 */

namespace Cundd\PersistentObjectStore\DataAccess;

use Cundd\PersistentObjectStore\AbstractDataBasedCase;
use Cundd\PersistentObjectStore\Configuration\ConfigurationManager;
use Cundd\PersistentObjectStore\Domain\Model\Data;
use Cundd\PersistentObjectStore\Domain\Model\Database;
use Cundd\PersistentObjectStore\Driver\Driver;
use Cundd\PersistentObjectStore\Utility\DebugUtility;

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
	public function readTestsCongressMembers() {
		/** @var Database $database */
		$database = $this->fixture->getDataByDatabase('congress_members');
		$this->assertEquals(4800, $database->count());
	}

	/**
	 * @test
	 */
	public function readTests() {
		/** @var Database $database */
		$database = $this->fixture->getDataByDatabase('contacts');
		$this->assertEquals(5, $database->count());
	}

	/**
	 * @test
	 */
	public function commitDatabaseTest() {
		/** @var Database $database */
		$database = $this->fixture->getDataByDatabase('contacts');

		$dataInstance = new Data();
		$dataInstance->setData(array(
			'firstName' => 'Oliver',
			'lastName'  => 'Kane',
			'email'     => 'o@kane.net'
		));
		$database->add($dataInstance);
		$this->fixture->commitDatabase($database);

		$expectedPath = ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('writeDataPath') . 'contacts.json';
		$this->assertTrue(file_exists($expectedPath));
		unlink($expectedPath);
	}

	/**
	 * @test
	 */
	public function commitBigDatabaseTest() {
		/** @var Database $database */
		$database = $this->fixture->getDataByDatabase('congress_members');

		$dataInstance = new Data();
		$dataInstance->setData(array(
			'congress_numbers' => array(102),
			'current'          => FALSE,
			'description'      => '',
			'district'         => 1,
			'enddate'          => '2014-10-09',
			'id'               => 20000,
			'leadership_title' => NULL,
			'party'            => 'Avengers',
			'person'           => array(
				'bioguideid'   => 'A000014',
				'birthday'     => '1986-11-13',
				'cspanid'      => NULL,
				'firstname'    => 'Daniel',
				'gender'       => 'male',
				'gender_label' => 'Male',
				'id'           => 400001,
				'lastname'     => 'Corn',
				'link'         => 'http://www.cundd.net',
				'middlename'   => '',
				'name'         => 'Avenger Corn Daniel [D-HI1, 1991-2010]',
				'namemod'      => '',
				'nickname'     => '',
				'osid'         => 'N00007665',
				'pvsid'        => '26827',
				'sortname'     => 'Corn, Daniel (Ave.) [D-HI1, 1991-2010]',
				'twitterid'    => NULL,
				'youtubeid'    => NULL
			),
			'phone'            => NULL,
			'role_type'        => 'representative',
			'role_type_label'  => 'Representative',
			'senator_class'    => NULL,
			'senator_rank'     => NULL,
			'startdate'        => '1991-01-03',
			'state'            => 'HI',
			'title'            => 'Rep.',
			'title_long'       => 'Representative',
			'website'          => 'http://www.cundd.net'
		));
		$database->add($dataInstance);
		$this->fixture->commitDatabase($database);

		$expectedPath = ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('writeDataPath') . 'congress_members.json';
		$this->assertTrue(file_exists($expectedPath));
		unlink($expectedPath);
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
			->setParameter(0, 'spm@cundd.net');
		$database = $this->fixture->getDataByQuery($queryBuilder);
		$this->assertSame($this->getAllTestData(), $this->databaseToDataArray($database));


		return;
		$queryBuilder->select('c')
			->innerJoin('c.lastName', 'contacts', 'ON', $queryBuilder->expr()->eq('p.lastName', ':lastName'))
			->where('c.email = :email');

		$queryBuilder->setParameters(array(
			'email'    => 'spm@cundd.net',
			'lastName' => 'Jobs',
		));

		$database = $this->fixture->getDataByQuery($queryBuilder);
		$this->assertSame($this->getAllTestData(), $this->databaseToDataArray($database));
	}
}
 