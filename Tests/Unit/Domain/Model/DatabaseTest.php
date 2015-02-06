<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 20.09.14
 * Time: 12:47
 */

namespace Cundd\PersistentObjectStore\Domain\Model;


use Cundd\PersistentObjectStore\AbstractDatabaseBasedCase;
use Cundd\PersistentObjectStore\Constants;
use Cundd\PersistentObjectStore\Filter\Comparison\ComparisonInterface;
use Cundd\PersistentObjectStore\Filter\Comparison\PropertyComparison;
use Cundd\PersistentObjectStore\Index\IndexInterface;

class DatabaseTest extends AbstractDatabaseBasedCase
{
    /**
     * @var \Cundd\PersistentObjectStore\Domain\Model\Database
     */
    protected $fixture;

    /**
     * @var \Cundd\PersistentObjectStore\DataAccess\Coordinator
     */
    protected $coordinator;

    /**
     * @test
     * @expectedException \Cundd\PersistentObjectStore\DataAccess\Exception\ReaderException
     */
    public function invalidDatabaseTest()
    {
        $this->coordinator = $this->getDiContainer()->get('\Cundd\PersistentObjectStore\DataAccess\Coordinator');
        $this->coordinator->getDatabase('congress_members');
    }

    /**
     * @test
     */
    public function findByIdentifierTest()
    {
        $person = $this->fixture->findByIdentifier('beasleywatts@geekol.com');
        $this->assertNotNull($person);

        $this->assertSame(22, $person->valueForKeyPath('age'));
        $this->assertSame('blue', $person->valueForKeyPath('eyeColor'));
        $this->assertSame('Beasley Watts', $person->valueForKeyPath('name'));
        $this->assertSame('male', $person->valueForKeyPath('gender'));

        $this->fixture = $this->coordinator->getDatabase('contacts');
        $person        = $this->fixture->findByIdentifier('paul@mckenzy.net');
        $this->assertNotNull($person);

        $this->assertSame('McKenzy', $person->valueForKeyPath('lastName'));
        $this->assertSame('Paul', $person->valueForKeyPath('firstName'));


        $person = $this->fixture->findByIdentifier('rob@ells.on');
        $this->assertNotNull($person);

        $this->assertSame('Ellson', $person->valueForKeyPath('lastName'));
        $this->assertSame('Robert', $person->valueForKeyPath('firstName'));
    }

    /**
     * @test
     */
    public function containsTest()
    {
        $this->fixture = $this->coordinator->getDatabase('contacts');

        $dataInstance = new Document(array('email' => 'info@cundd.net'), $this->fixture->getIdentifier());
        $this->assertTrue($this->fixture->contains($dataInstance));
        $this->assertTrue($this->fixture->contains('info@cundd.net'));

        $dataInstance = new Document(array('email' => 'paul@mckenzy.net'), $this->fixture->getIdentifier());
        $this->assertTrue($this->fixture->contains($dataInstance));
        $this->assertTrue($this->fixture->contains('paul@mckenzy.net'));

        $dataInstance = new Document(array('email' => 'rob@ells.on'), $this->fixture->getIdentifier());
        $this->assertTrue($this->fixture->contains($dataInstance));
        $this->assertTrue($this->fixture->contains('rob@ells.on'));

        $dataInstance = new Document(array('email' => 'info-not-found@cundd.net'), $this->fixture->getIdentifier());
        $this->assertFalse($this->fixture->contains($dataInstance));
        $this->assertFalse($this->fixture->contains('info-not-found@cundd.net'));
    }

    /**
     * @test
     */
    public function containsHeavyTest()
    {
        $i = 0;
        do {
            $this->assertTrue($this->fixture->contains('elnorahall@filodyne.com'));
        } while (++$i < 1000);

        $i = 0;
        do {
            $this->assertFalse($this->fixture->contains("something-thats-not@there-$i.com"));
        } while (++$i < 1000);
    }

    /**
     * @test
     */
    public function addTest()
    {
        $this->fixture = $this->coordinator->getDatabase('contacts');

        $testEmail    = 'mail' . time() . '@test.com';
        $dataInstance = new Document(
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
    public function removeTest()
    {
        $this->fixture = $this->coordinator->getDatabase('contacts');

        $testEmail    = 'alice@mckenzy.net';
        $dataInstance = new Document(array('email' => $testEmail), $this->fixture->getIdentifier());

        $this->fixture->remove($dataInstance);

        $this->assertTrue(!$this->fixture->contains($dataInstance));
        $this->assertTrue(!$this->fixture->contains($testEmail));
    }

    /**
     * @test
     */
    public function addAndGetStateTest()
    {
        $this->fixture = $this->coordinator->getDatabase('contacts');
        $this->assertEquals(DatabaseStateInterface::STATE_CLEAN, $this->fixture->getState());

        $this->fixture->add(new Document(
            array(
                'email'    => 'mail' . time() . '@test.com',
                'age'      => 31,
                'eyeColor' => 'green'
            ),
            $this->fixture->getIdentifier()
        ));

        $this->assertEquals(DatabaseStateInterface::STATE_DIRTY, $this->fixture->getState());
        $this->coordinator->commitDatabase($this->fixture);
        $this->assertEquals(DatabaseStateInterface::STATE_CLEAN, $this->fixture->getState());


        $this->fixture->add(new Document(
            array(
                'email'    => 'mail-2-' . time() . '@test.com',
                'age'      => 32,
                'eyeColor' => 'brown'
            ),
            $this->fixture->getIdentifier()
        ));

        $this->assertEquals(DatabaseStateInterface::STATE_DIRTY, $this->fixture->getState());
        $this->coordinator->commitDatabases();
        $this->assertEquals(DatabaseStateInterface::STATE_CLEAN, $this->fixture->getState());
    }

    /**
     * @test
     */
    public function removeAndGetStateTest()
    {
        $this->fixture = $this->coordinator->getDatabase('contacts');
        $this->assertEquals(DatabaseStateInterface::STATE_CLEAN, $this->fixture->getState());

        $this->fixture->remove(new Document(array('email' => 'alice@mckenzy.net'), $this->fixture->getIdentifier()));
        $this->assertEquals(DatabaseStateInterface::STATE_DIRTY, $this->fixture->getState());
        $this->coordinator->commitDatabase($this->fixture);
        $this->assertEquals(DatabaseStateInterface::STATE_CLEAN, $this->fixture->getState());


        $this->fixture->remove(new Document(array('email' => 'paul@mckenzy.net'), $this->fixture->getIdentifier()));
        $this->assertEquals(DatabaseStateInterface::STATE_DIRTY, $this->fixture->getState());
        $this->coordinator->commitDatabases();
        $this->assertEquals(DatabaseStateInterface::STATE_CLEAN, $this->fixture->getState());
    }

    /**
     * @test
     */
    public function addAndFindByIdentifierTest()
    {
        $testEmail    = 'mail' . time() . '@test.com';
        $dataInstance = new Document(
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
    public function addAndFilterTest()
    {
        $testEmail    = 'my-mail-' . time() . '@test.com';
        $dataInstance = new Document(
            array(
                'email'    => $testEmail,
                'age'      => 31,
                'eyeColor' => 'green'
            ),
            $this->fixture->getIdentifier()
        );

        $this->fixture->add($dataInstance);

        // First check if the Document instance was added
        $person = $this->fixture->findByIdentifier($testEmail);
        $this->assertNotNull($person);

        // Now really test the filter
        $filterResult = $this->fixture->filter(
            new PropertyComparison('email', ComparisonInterface::TYPE_EQUAL_TO, $testEmail)
        );
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertGreaterThan(0, $filterResult->count());

        $person = $filterResult->current();
        $this->assertNotNull($person);

        $this->assertSame(31, $person->valueForKeyPath('age'));
        $this->assertSame('green', $person->valueForKeyPath('eyeColor'));
    }

    /**
     * @test
     */
    public function toArrayTest()
    {
        $this->fixture = $this->coordinator->getDatabase('contacts');
        $this->assertEquals($this->getAllTestData(), $this->databaseToDataArray($this->fixture));
        $this->assertEquals($this->getAllTestObjects(), $this->fixture->toArray());
    }

    /**
     * A test that should validate the behavior of data object references in a database
     *
     * @test
     */
    public function objectLiveCycleTest()
    {
        $database1 = $this->coordinator->getDatabase('people-small');
        $database2 = $this->coordinator->getDatabase('people-small');

        /** @var DocumentInterface $personFromDatabase2 */
        $personFromDatabase2 = $database2->current();

        /** @var DocumentInterface $personFromFixture */
        $personFromFixture = $database1->current();

        $this->assertEquals($personFromDatabase2, $personFromFixture);

        $movie = 'Star Wars';
        $key   = 'favorite_movie';

        $personFromDatabase2->setValueForKey($movie, $key);

        $this->assertEquals($personFromDatabase2, $personFromFixture);
        $this->assertSame($personFromDatabase2, $personFromFixture);
        $this->assertEquals($movie, $personFromFixture->valueForKey($key));
        $this->assertEquals($movie, $personFromDatabase2->valueForKey($key));
    }

    /**
     * @test
     */
    public function hasIndexesForValueOfPropertyTest()
    {
        $this->assertTrue($this->fixture->hasIndexesForValueOfProperty('an-id', Constants::DATA_ID_KEY));
        $this->assertFalse($this->fixture->hasIndexesForValueOfProperty('Cundd Lane 1', 'address'));
    }

    /**
     * @test
     */
    public function getIndexesForValueOfPropertyTest()
    {
        $this->assertEmpty($this->fixture->getIndexesForValueOfProperty('Cundd Lane 1', 'address'));

        $indexes = $this->fixture->getIndexesForValueOfProperty('an-id', Constants::DATA_ID_KEY);
        $this->assertNotEmpty($indexes);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Index\\IdentifierIndex', $indexes[0]);
    }

    /**
     * @test
     */
    public function queryIndexesForValueOfPropertyTest()
    {
        $this->assertSame(IndexInterface::NO_RESULT,
            $this->fixture->queryIndexesForValueOfProperty('Cundd Lane 1', 'address'));
        $this->assertSame(IndexInterface::NOT_FOUND,
            $this->fixture->queryIndexesForValueOfProperty('not-existing-identifier', Constants::DATA_ID_KEY));

        $documents = $this->fixture->queryIndexesForValueOfProperty(
            'beasleywatts@geekol.com',
            Constants::DATA_ID_KEY
        );
        $this->assertNotEmpty($documents);
        $this->assertInternalType('array', $documents);

        /** @var DocumentInterface $person */
        $person = $documents[0];

        $this->assertNotNull($person);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Domain\\Model\\Document', $person);

        $this->assertSame(22, $person->valueForKeyPath('age'));
        $this->assertSame('blue', $person->valueForKeyPath('eyeColor'));
        $this->assertSame('Beasley Watts', $person->valueForKeyPath('name'));
        $this->assertSame('male', $person->valueForKeyPath('gender'));
    }

    protected function setUp()
    {
        $this->checkPersonFile();

        $this->setUpXhprof();

        $this->coordinator = $this->getDiContainer()->get('\Cundd\PersistentObjectStore\DataAccess\Coordinator');
        $this->fixture = $this->getSmallPeopleDatabase();
    }

    protected function tearDown()
    {
//		unset($this->fixture);
//		unset($this->coordinator);
        parent::tearDown();
    }


}
 