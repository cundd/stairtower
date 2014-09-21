<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 20.09.14
 * Time: 12:47
 */

namespace Cundd\PersistentObjectStore\Domain\Model;


use Cundd\PersistentObjectStore\AbstractCase;
use Cundd\PersistentObjectStore\Utility\DebugUtility;

class DatabaseTest extends AbstractCase {
	/**
	 * @var \Cundd\PersistentObjectStore\Domain\Model\Database
	 */
	protected $fixture;

	/**
	 * @var \Cundd\PersistentObjectStore\DataAccess\Coordinator
	 */
	protected $coordinator;

	protected function setUp() {
		$this->checkCongressMemberFile();

		$this->setUpXhprof();

		/** @var \Cundd\PersistentObjectStore\DataAccess\Coordinator $coordinator */
		$this->coordinator = $this->getDiContainer()->get('\Cundd\PersistentObjectStore\DataAccess\Coordinator');
		$this->fixture = $this->coordinator->getDataByDatabase('congress_members');
	}

	protected function tearDown() {
//		unset($this->fixture);
//		unset($this->coordinator);
		$this->tearDownXhprof();
	}

	/**
	 * @test
	 */
	public function findByIdentifierTest() {
		$congressMember = $this->fixture->findByIdentifier(1);
		$this->assertSame('Neil', $congressMember->valueForKeyPath('person.firstname'));
	}

	/**
	 * A test that should validate the behavior of data object references in a database
	 *
	 * @test
	 */
	public function objectLiveCycleTest() {
		$database2 = $this->coordinator->getDataByDatabase('congress_members');

		/** @var DataInterface $memberFromDatabase2 */
		$memberFromDatabase2 = $database2->current();

		/** @var DataInterface $memberFromFixture */
		$memberFromFixture = $this->fixture->current();

		$this->assertEquals($memberFromDatabase2, $memberFromFixture);

		$movie = 'Star Wars';
		$key = 'favorite_movie';

		$memberFromDatabase2->setValueForKey($movie, $key);

		$this->assertEquals($memberFromDatabase2, $memberFromFixture);
		$this->assertSame($memberFromDatabase2, $memberFromFixture);
		$this->assertEquals($movie, $memberFromFixture->valueForKey($key));
		$this->assertEquals($movie, $memberFromDatabase2->valueForKey($key));
	}


}
 