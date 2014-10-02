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
use Cundd\PersistentObjectStore\Domain\Model\DatabaseInterface;
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
	 * Number of congress members in the database
	 *
	 * @var int
	 */
	protected $numberOfPersons = 4998;

	/**
	 * @var \Cundd\PersistentObjectStore\DataAccess\Reader
	 */
	protected $databaseReader;

	protected function setUp() {
		parent::setUp();
		$this->databaseReader = $this->getDiContainer()->get('\Cundd\PersistentObjectStore\DataAccess\Reader');
	}


	/**
	 * @test
	 */
	public function readTestsPersons() {
		$this->checkPersonFile();

		/** @var Database $database */
		$database = $this->fixture->getDataByDatabase('people');
		$this->assertEquals($this->numberOfPersons, $database->count());
		$this->assertEquals($this->numberOfPersons, count(iterator_to_array($database)));
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
		$this->checkPersonFile();

		/** @var Database $database */
		$database = $this->fixture->getDataByDatabase('people');

		$dataInstance = new Data();
		$dataInstance->setData(array(
			'_id'           => '541f004ef8f4d2df32ca60c2',
			'index'         => 5000,
			'isActive'      => FALSE,
			'balance'       => '$2,925.56',
			'picture'       => 'http://placehold.it/32x32',
			'age'           => 31,
			'eyeColor'      => 'brown',
			'name'          => 'Daniel Corn',
			'gender'        => 'male',
			'company'       => 'FARMEX',
			'email'         => 'info@cundd.net',
			'phone'         => '+1 (973) 480-3194',
			'address'       => '125 Stone Avenue, Worton, Alabama, 6669',
			'about'         => 'Dolore in excepteur nisi dolor laboris ipsum proident cupidatat proident. Aliquip commodo culpa adipisicing ullamco ad. Ut ex duis tempor do id enim. Proident exercitation officia veniam magna mollit nostrud duis do qui reprehenderit. Ea culpa anim ullamco aliqua culpa nulla ex nisi irure qui incididunt reprehenderit. Labore do velit amet duis aute occaecat. Et sunt ex Lorem qui do deserunt ullamco labore.\r\n',
			'registered'    => '2014-06-29T15:29:47 -02:00',
			'latitude'      => 52.372838,
			'longitude'     => -70.88925,
			'tags'          => [
				'id',
				'consequat',
				'aute',
				'deserunt',
				'in',
				'enim',
				'veniam'
			],
			'friends'       => [
				array(
					'id'   => 0,
					'name' => 'Bray Ruiz'
				),
				array(
					'id'   => 1,
					'name' => 'Carr Kerr'
				),
				array(
					'id'   => 2,
					'name' => 'Carter Dejesus'
				)
			],
			'greeting'      => 'Hello, Conway Burch! You have 3 unread messages.',
			'favoriteFruit' => 'apple'
		));

		$database->add($dataInstance);
		$this->assertEquals($this->numberOfPersons + 1, $database->count());

		$this->fixture->commitDatabase($database);

		$expectedPath = ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('writeDataPath') . 'people.json';
		$this->assertTrue(file_exists($expectedPath));
		unlink($expectedPath);
	}

	/**
	 * @test
	 */
	public function addToDatabaseTest() {
		$this->checkPersonFile();

		/** @var Database $database */
		$database = $this->fixture->getDataByDatabase('people');

		$dataInstance = new Data();
		$dataInstance->setData(array(
			'_id'           => '541f004ef8f4d2df32ca60c2',
			'index'         => 5000,
			'isActive'      => FALSE,
			'balance'       => '$2,925.56',
			'picture'       => 'http://placehold.it/32x32',
			'age'           => 31,
			'eyeColor'      => 'brown',
			'name'          => 'Daniel Corn',
			'gender'        => 'male',
			'company'       => 'FARMEX',
			'email'         => 'info2@cundd.net',
			'phone'         => '+1 (973) 480-3194',
			'address'       => '125 Stone Avenue, Worton, Alabama, 6669',
			'about'         => 'Dolore in excepteur nisi dolor laboris ipsum proident cupidatat proident. Aliquip commodo culpa adipisicing ullamco ad. Ut ex duis tempor do id enim. Proident exercitation officia veniam magna mollit nostrud duis do qui reprehenderit. Ea culpa anim ullamco aliqua culpa nulla ex nisi irure qui incididunt reprehenderit. Labore do velit amet duis aute occaecat. Et sunt ex Lorem qui do deserunt ullamco labore.\r\n',
			'registered'    => '2014-06-29T15:29:47 -02:00',
			'latitude'      => 52.372838,
			'longitude'     => -70.88925,
			'tags'          => [
				'id',
				'consequat',
				'aute',
				'deserunt',
				'in',
				'enim',
				'veniam'
			],
			'friends'       => [
				array(
					'id'   => 0,
					'name' => 'Bray Ruiz'
				),
				array(
					'id'   => 1,
					'name' => 'Carr Kerr'
				),
				array(
					'id'   => 2,
					'name' => 'Carter Dejesus'
				)
			],
			'greeting'      => 'Hello, Conway Burch! You have 3 unread messages.',
			'favoriteFruit' => 'apple'
		));

		$database->add($dataInstance);
		$this->assertEquals($this->numberOfPersons + 1, $database->count());

		/** @var DatabaseInterface $newlyLoadedDatabase */
		$newlyLoadedDatabase = $this->databaseReader->loadDatabase('people');
		$this->assertEquals($this->numberOfPersons + 1, $newlyLoadedDatabase->count());
		$this->assertEquals($database->count(), $newlyLoadedDatabase->count());

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
 