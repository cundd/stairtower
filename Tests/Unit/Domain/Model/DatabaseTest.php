<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 20.09.14
 * Time: 12:47
 */

namespace Cundd\PersistentObjectStore\Domain\Model;


use Cundd\PersistentObjectStore\AbstractCase;
use Cundd\PersistentObjectStore\Filter\Comparison\ComparisonInterface;
use Cundd\PersistentObjectStore\Filter\Comparison\PropertyComparison;
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
		$this->checkPersonFile();

		$this->setUpXhprof();

		$this->coordinator = $this->getDiContainer()->get('\Cundd\PersistentObjectStore\DataAccess\Coordinator');
		$this->fixture     = $this->coordinator->getDatabase('people');
	}

	protected function tearDown() {
//		unset($this->fixture);
//		unset($this->coordinator);
	}

	/**
	 * @test
	 * @expectedException \Cundd\PersistentObjectStore\DataAccess\Exception\ReaderException
	 */
	public function invalidDatabaseTest() {
		$this->coordinator = $this->getDiContainer()->get('\Cundd\PersistentObjectStore\DataAccess\Coordinator');
		$this->coordinator->getDatabase('congress_members');
	}

	/**
	 * @test
	 */
	public function findByIdentifierTest() {
		$person = $this->fixture->findByIdentifier('georgettebenjamin@andryx.com');
		$this->assertNotNull($person);

		$this->assertSame(31, $person->valueForKeyPath('age'));
		$this->assertSame('green', $person->valueForKeyPath('eyeColor'));
		$this->assertSame('Georgette Benjamin', $person->valueForKeyPath('name'));
		$this->assertSame('female', $person->valueForKeyPath('gender'));

		$this->fixture = $this->coordinator->getDatabase('contacts');
		$person        = $this->fixture->findByIdentifier('paul@mckenzy.net');
		$this->assertNotNull($person);

		$this->assertSame('McKenzy', $person->valueForKeyPath('lastName'));
		$this->assertSame('Paul', $person->valueForKeyPath('firstName'));
	}

	/**
	 * @test
	 */
	public function containsTest() {
		$this->fixture = $this->coordinator->getDatabase('contacts');

		$dataInstance = new Data(array('email' => 'info@cundd.net'), $this->fixture->getIdentifier());
		$this->assertTrue($this->fixture->contains($dataInstance));
		$this->assertTrue($this->fixture->contains('info@cundd.net'));

		$dataInstance = new Data(array('email' => 'paul@mckenzy.net'), $this->fixture->getIdentifier());
		$this->assertTrue($this->fixture->contains($dataInstance));
		$this->assertTrue($this->fixture->contains('paul@mckenzy.net'));

		$dataInstance = new Data(array('email' => 'info-not-found@cundd.net'), $this->fixture->getIdentifier());
		$this->assertFalse($this->fixture->contains($dataInstance));
		$this->assertFalse($this->fixture->contains('info-not-found@cundd.net'));
	}

	/**
	 * @test
	 */
	public function addTest() {
		$this->fixture = $this->coordinator->getDatabase('contacts');

		$testEmail    = 'mail' . time() . '@test.com';
		$dataInstance = new Data(
			array(
				'email'    => $testEmail,
				'age'      => 31,
				'eyeColor' => 'green'
			),
			$this->fixture->getIdentifier()
		);

		$this->fixture->add($dataInstance);

		$this->assertTrue($this->fixture->contains($dataInstance));
		$this->assertTrue($this->fixture->contains($testEmail));
	}

	/**
	 * @test
	 */
	public function removeTest() {
		$this->fixture = $this->coordinator->getDatabase('contacts');

		$testEmail    = 'alice@mckenzy.net';
		$dataInstance = new Data(array('email' => $testEmail), $this->fixture->getIdentifier());

		$this->fixture->remove($dataInstance);

		$this->assertTrue(!$this->fixture->contains($dataInstance));
		$this->assertTrue(!$this->fixture->contains($testEmail));
	}

	/**
	 * @test
	 */
	public function addAndFindByIdentifierTest() {
		$testEmail    = 'mail' . time() . '@test.com';
		$dataInstance = new Data(
			array(
				'email'    => $testEmail,
				'age'      => 31,
				'eyeColor' => 'green'
			),
			$this->fixture->getIdentifier()
		);

		$this->fixture->add($dataInstance);
		$person = $this->fixture->findByIdentifier($testEmail);
		$this->assertNotNull($person);

		$this->assertSame(31, $person->valueForKeyPath('age'));
		$this->assertSame('green', $person->valueForKeyPath('eyeColor'));
	}

	/**
	 * @test
	 */
	public function addAndFilterTest() {
		$testEmail    = 'my-mail-' . time() . '@test.com';
		$dataInstance = new Data(
			array(
				'email'    => $testEmail,
				'age'      => 31,
				'eyeColor' => 'green'
			),
			$this->fixture->getIdentifier()
		);

		$this->fixture->add($dataInstance);

		// First check if the Data instance was added
		$person = $this->fixture->findByIdentifier($testEmail);
		$this->assertNotNull($person);

		// Now really test the filter
		$filterResult = $this->fixture->filter(array(new PropertyComparison('email', ComparisonInterface::TYPE_EQUAL_TO, $testEmail)));
		$this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
		$this->assertGreaterThan(0, $filterResult->count());

		$person = $filterResult->current();
		$this->assertNotNull($person);

		$this->assertSame(31, $person->valueForKeyPath('age'));
		$this->assertSame('green', $person->valueForKeyPath('eyeColor'));
	}

	/**
	 * A test that should validate the behavior of data object references in a database
	 *
	 * @test
	 */
	public function objectLiveCycleTest() {
		$database2 = $this->coordinator->getDatabase('people');

		/** @var DataInterface $personFromDatabase2 */
		$personFromDatabase2 = $database2->current();

		/** @var DataInterface $personFromFixture */
		$personFromFixture = $this->fixture->current();

		$this->assertEquals($personFromDatabase2, $personFromFixture);

		$movie = 'Star Wars';
		$key   = 'favorite_movie';

		$personFromDatabase2->setValueForKey($movie, $key);

		$this->assertEquals($personFromDatabase2, $personFromFixture);
		$this->assertSame($personFromDatabase2, $personFromFixture);
		$this->assertEquals($movie, $personFromFixture->valueForKey($key));
		$this->assertEquals($movie, $personFromDatabase2->valueForKey($key));
	}


}
 