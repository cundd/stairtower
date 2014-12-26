<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 31.08.14
 * Time: 16:13
 */

namespace Cundd\PersistentObjectStore\Filter;

use Cundd\PersistentObjectStore\AbstractDatabaseBasedCase;
use Cundd\PersistentObjectStore\Domain\Model\Document;
use Cundd\PersistentObjectStore\Domain\Model\DocumentInterface;
use Cundd\PersistentObjectStore\Filter\Comparison\ComparisonInterface;
use Cundd\PersistentObjectStore\Filter\Comparison\LogicalComparison;
use Cundd\PersistentObjectStore\Filter\Comparison\PropertyComparison;
use PHPUnit_Framework_TestCase;

/**
 * Test for Cundd\PersistentObjectStore\Filter\FilterResult
 *
 * @package Cundd\PersistentObjectStore\Filter
 */
class FilterResultTest extends AbstractDatabaseBasedCase
{
    /**
     * @var \Cundd\PersistentObjectStore\Filter\FilterResult
     */
    protected $fixture;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @test
     */
    public function currentTest()
    {
        $currentObject = $this->fixture->current();
        $this->assertNotNull($currentObject);

        $this->assertNotNull($this->fixture->current());
        $this->assertSame($currentObject, $this->fixture->current());

        $this->assertSame('Nolan Byrd', $this->fixture->current()->valueForKeyPath('name'));
        $this->assertContains('labore', $this->fixture->current()->valueForKeyPath('tags'));

        $this->fixture->next();
        $this->assertNotNull($this->fixture->current());

        $this->assertSame('Booker Oneil', $this->fixture->current()->valueForKeyPath('name'));
        $this->assertContains('laboris', $this->fixture->current()->valueForKeyPath('tags'));
    }

    /**
     * @test
     */
    public function currentWithMoreConstraintsTest()
    {
        /** @var \Cundd\PersistentObjectStore\DataAccess\Coordinator $coordinator */
        $coordinator = $this->getDiContainer()->get('\Cundd\PersistentObjectStore\DataAccess\Coordinator');

        $filter = new Filter(array(
            new PropertyComparison('eyeColor', ComparisonInterface::TYPE_EQUAL_TO, 'green'),
            new PropertyComparison('name', ComparisonInterface::TYPE_EQUAL_TO, 'Booker Oneil'),
        ));

        $database     = $coordinator->getDatabase('people');
        $filterResult = $filter->filterCollection($database);

        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertNotNull($filterResult->current());

        $this->assertSame('Booker Oneil', $filterResult->current()->valueForKeyPath('name'));
        $this->assertContains('laboris', $filterResult->current()->valueForKeyPath('tags'));
    }

    /**
     * @test
     */
    public function currentWithMoreConstraintsAddTest()
    {
        /** @var \Cundd\PersistentObjectStore\DataAccess\Coordinator $coordinator */
        $coordinator = $this->getDiContainer()->get('\Cundd\PersistentObjectStore\DataAccess\Coordinator');

        $filter = new Filter();

        $database = $coordinator->getDatabase('people');
        $filter->addComparison(new PropertyComparison('eyeColor', ComparisonInterface::TYPE_EQUAL_TO, 'green'));
        $filter->addComparison(new PropertyComparison('name', ComparisonInterface::TYPE_EQUAL_TO, 'Booker Oneil'));
        $filterResult = $filter->filterCollection($database);

        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertNotNull($filterResult->current());

        $this->assertSame('Booker Oneil', $filterResult->current()->valueForKeyPath('name'));
        $this->assertContains('laboris', $filterResult->current()->valueForKeyPath('tags'));
    }

    /**
     * @test
     */
    public function currentWithNestedConstraintsTest()
    {
        $database = $this->getSmallPeopleDatabase();
        $filter   = new Filter();
        $filter->addComparison(
            new LogicalComparison(
                ComparisonInterface::TYPE_OR,
                new LogicalComparison(
                    ComparisonInterface::TYPE_AND,
                    new PropertyComparison('eyeColor', ComparisonInterface::TYPE_EQUAL_TO, 'blue'),
                    new PropertyComparison('age', ComparisonInterface::TYPE_LESS_THAN, 25)
                ),
                new LogicalComparison(
                    ComparisonInterface::TYPE_AND,
                    new PropertyComparison('eyeColor', ComparisonInterface::TYPE_EQUAL_TO, 'green'),
                    new PropertyComparison('age', ComparisonInterface::TYPE_LESS_THAN, 25)
                )
            )
        );
        $filterResult = $filter->filterCollection($database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertEquals(25, $filterResult->count());

        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertSame('angelaroberts@cundd.net', $currentObject->valueForKey('email'));
        $this->assertSame('Angela Roberts', $currentObject->valueForKey('name'));
        $this->assertContains('tempor', $currentObject->valueForKey('tags'));
        $this->assertContains('blue', $currentObject->valueForKey('eyeColor'));

        $filterResult->next();
        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertSame('elliottgentry@andershun.com', $currentObject->valueForKey('email'));
        $this->assertSame('Elliott Gentry', $currentObject->valueForKey('name'));
        $this->assertContains('aliqua', $currentObject->valueForKey('tags'));
        $this->assertContains('green', $currentObject->valueForKey('eyeColor'));


        $filter = new Filter();
        $filter->addComparison(
            new LogicalComparison(
                ComparisonInterface::TYPE_AND,
                [
                    new LogicalComparison(
                        ComparisonInterface::TYPE_OR,
                        [
                            new PropertyComparison('eyeColor', ComparisonInterface::TYPE_EQUAL_TO, 'blue'),
                            new PropertyComparison('eyeColor', ComparisonInterface::TYPE_EQUAL_TO, 'green')
                        ]
                    ),
                    new PropertyComparison('age', ComparisonInterface::TYPE_LESS_THAN, 25)
                ]
            )
        );

        $filterResult = $filter->filterCollection($database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertEquals(25, $filterResult->count());

        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertSame('angelaroberts@cundd.net', $currentObject->valueForKey('email'));
        $this->assertSame('Angela Roberts', $currentObject->valueForKey('name'));
        $this->assertContains('tempor', $currentObject->valueForKey('tags'));
        $this->assertContains('blue', $currentObject->valueForKey('eyeColor'));

        $filterResult->next();
        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertSame('elliottgentry@andershun.com', $currentObject->valueForKey('email'));
        $this->assertSame('Elliott Gentry', $currentObject->valueForKey('name'));
        $this->assertContains('aliqua', $currentObject->valueForKey('tags'));
        $this->assertContains('green', $currentObject->valueForKey('eyeColor'));


        $filter = new Filter();
        $filter->addComparison(
            new LogicalComparison(
                ComparisonInterface::TYPE_AND,
                new LogicalComparison(
                    ComparisonInterface::TYPE_OR,
                    new PropertyComparison('eyeColor', ComparisonInterface::TYPE_EQUAL_TO, 'blue'),
                    new PropertyComparison('eyeColor', ComparisonInterface::TYPE_EQUAL_TO, 'green')
                ),
                new PropertyComparison('age', ComparisonInterface::TYPE_LESS_THAN, 25)
            )
        );

        $filterResult = $filter->filterCollection($database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertEquals(25, $filterResult->count());

        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertSame('angelaroberts@cundd.net', $currentObject->valueForKey('email'));
        $this->assertSame('Angela Roberts', $currentObject->valueForKey('name'));
        $this->assertContains('tempor', $currentObject->valueForKey('tags'));
        $this->assertContains('blue', $currentObject->valueForKey('eyeColor'));

        $filterResult->next();
        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertSame('elliottgentry@andershun.com', $currentObject->valueForKey('email'));
        $this->assertSame('Elliott Gentry', $currentObject->valueForKey('name'));
        $this->assertContains('aliqua', $currentObject->valueForKey('tags'));
        $this->assertContains('green', $currentObject->valueForKey('eyeColor'));
    }

    /**
     * @test
     */
    public function nextTest()
    {
        $this->fixture->next();
        $this->assertNotNull($this->fixture->current());
        $this->assertSame('Booker Oneil', $this->fixture->current()->valueForKeyPath('name'));
        $this->assertContains('laboris', $this->fixture->current()->valueForKeyPath('tags'));
    }

    /**
     * @test
     */
    public function countTest()
    {
        $this->fixture->next();
        $this->fixture->next();
        $this->fixture->next();
        $this->fixture->next();
        $this->assertEquals(1713, $this->fixture->count());
        $this->assertNotNull($this->fixture->current());

        iterator_to_array($this->fixture);
        $this->assertEquals(1713, $this->fixture->count());
    }

    /**
     * @test
     * @expectedException \Cundd\PersistentObjectStore\Core\ArrayException\IndexOutOfRangeException
     */
    public function iterateAndGetCurrentShouldThrowAnException()
    {
        $this->assertEquals(1713, $this->fixture->count());
        $this->assertNotNull($this->fixture->current());

        iterator_to_array($this->fixture);
        $this->assertEquals(1713, $this->fixture->count());
        $this->assertNotNull($this->fixture->current());
    }

    /**
     * @test
     */
    public function doALotOfThings()
    {
        $exception = null;
        $this->assertEquals(1713, $this->fixture->count());
        $this->assertNotNull($this->fixture->current());

        iterator_to_array($this->fixture);
        $this->assertEquals(1713, $this->fixture->count());

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
    public function objectLiveCycleTest()
    {
        /** @var \Cundd\PersistentObjectStore\DataAccess\Coordinator $coordinator */
        $coordinator = $this->getDiContainer()->get('\Cundd\PersistentObjectStore\DataAccess\Coordinator');

        $newFilter = new Filter();

        $database = $coordinator->getDatabase('people');
        $newFilter->addComparison(new PropertyComparison('eyeColor', ComparisonInterface::TYPE_EQUAL_TO, 'green'));
        $newFilterResult = $newFilter->filterCollection($database);

        /** @var DocumentInterface $memberFromNewFilter */
        $memberFromNewFilter = $newFilterResult->current();

        /** @var DocumentInterface $memberFromFixture */
        $memberFromFixture = $this->fixture->current();

        $this->assertEquals($memberFromNewFilter, $memberFromFixture);

        $movie = 'Star Wars';
        $key   = 'favorite_movie';

        $memberFromNewFilter->setValueForKey($movie, $key);
        $this->assertEquals($memberFromNewFilter, $memberFromFixture);
        $this->assertEquals(spl_object_hash($memberFromNewFilter), spl_object_hash($memberFromFixture));
        $this->assertEquals($movie, $memberFromNewFilter->valueForKey($key));
        $this->assertEquals($movie, $memberFromFixture->valueForKey($key));

    }

    /**
     * @test
     */
    public function filterNormalCollectionTest()
    {
        $exampleCollection = new \SplObjectStorage();
        $exampleCollection->attach(new Document(array(
            'name'    => 'Red Hot Chili Peppers',
            'founded' => 1983,
            'breakUp' => null,
            'url'     => 'http://www.redhotchilipeppers.com/',
        )));

        $exampleCollection->attach(new Document(array(
            'name'    => 'The Beatles',
            'founded' => 1960,
            'breakUp' => 1970,
            'url'     => 'http://thebeatles.com/',
        )));

        $exampleCollection->attach(new Document(array(
            'name'    => 'Pink Floyd',
            'founded' => 1965,
            'breakUp' => 2014,
            'url'     => 'http://thebeatles.com/',
        )));

        $filter       = new Filter(new PropertyComparison('breakUp', ComparisonInterface::TYPE_LESS_THAN_OR_EQUAL_TO,
            2014));
        $filterResult = $filter->filterCollection($exampleCollection);

        $this->assertEquals(3, $filterResult->count());


        $exampleCollection->attach(new Document(array(
            'name'    => 'Starflyer 59',
            'founded' => 1993,
            'breakUp' => null,
            'url'     => 'http://www.sf59.com/',
        )));

        $this->assertEquals(3, $filterResult->count());
    }

    protected function setUp()
    {
        $this->checkPersonFile();

        $this->setUpXhprof();

        /** @var \Cundd\PersistentObjectStore\DataAccess\Coordinator $coordinator */
        $coordinator = $this->getDiContainer()->get('\Cundd\PersistentObjectStore\DataAccess\Coordinator');

        $database = $coordinator->getDatabase('people');

        $this->filter = new Filter();
        $this->filter->addComparison(new PropertyComparison('eyeColor', ComparisonInterface::TYPE_EQUAL_TO, 'green'));
        $this->fixture = $this->filter->filterCollection($database);
    }

    protected function tearDown()
    {
        unset($this->filter);
        unset($this->fixture);
        parent::tearDown();
    }
}
 