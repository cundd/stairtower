<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 29.09.14
 * Time: 22:06
 */

namespace Cundd\PersistentObjectStore\Sorting;


use Cundd\PersistentObjectStore\AbstractDataBasedCase;
use Cundd\PersistentObjectStore\Domain\Model\Database;
use Cundd\PersistentObjectStore\Domain\Model\DataInterface;
use Cundd\PersistentObjectStore\Utility\DebugUtility;

/**
 * Test for the sorting
 *
 * @package Cundd\PersistentObjectStore\Sorting
 */
class SorterTest extends AbstractDataBasedCase {
	/**
	 * @var \Cundd\PersistentObjectStore\Sorting\Sorter
	 */
	protected $fixture;

	/**
	 * @var \Cundd\PersistentObjectStore\DataAccess\Coordinator
	 */
	protected $coordinator;

	protected function setUp() {
		$this->checkPersonFile();

//		$this->setUpXhprof();

		$this->coordinator = $this->getDiContainer()->get('\Cundd\PersistentObjectStore\DataAccess\Coordinator');
		$this->fixture = new Sorter();
	}

	protected function tearDown() {
		unset($this->fixture);
//		unset($this->coordinator);
		$this->tearDownXhprof();
	}


	/**
	 * @test
	 */
	public function sortPersonsTest() {
		$this->checkPersonFile();

		/** @var Database $database */
		$database = $this->coordinator->getDataByDatabase('people');

//		$this->fixture->setSortFlags(SORT_LOCALE_STRING);
		$sortedDatabase = $this->fixture->sortCollectionByPropertyKeyPath($database, 'latitude');

		$lastLatitude = -PHP_INT_MAX;
		for ($i = 0; $i < 100; $i++) {
			/** @var DataInterface $item */
			$item = $sortedDatabase[$i++];
			$this->assertNotNull($item);

			DebugUtility::var_dump($item->valueForKey('latitude'));

			$this->assertGreaterThan($lastLatitude, $item->valueForKey('latitude'));
			$lastLatitude = $item->valueForKey('latitude');
		}


//		$this->fixture->setSortFlags(SORT_LOCALE_STRING);
		$sortedDatabase = $this->fixture->sortCollectionByPropertyKeyPath($database, 'latitude', TRUE);

		$lastLatitude = PHP_INT_MAX;
		for ($i = 0; $i < 100; $i++) {
			/** @var DataInterface $item */
			$item = $sortedDatabase[$i++];
			$this->assertNotNull($item);

			DebugUtility::var_dump($item->valueForKey('latitude'));

			$this->assertLessThan($lastLatitude, $item->valueForKey('latitude'));
			$lastLatitude = $item->valueForKey('latitude');
		}
	}

}
 