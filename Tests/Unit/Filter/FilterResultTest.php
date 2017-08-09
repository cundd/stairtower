<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Tests\Unit\Filter;

use Cundd\Stairtower\DataAccess\Coordinator;
use Cundd\Stairtower\Domain\Model\Document;
use Cundd\Stairtower\Domain\Model\DocumentInterface;
use Cundd\Stairtower\Filter\Comparison\ComparisonInterface;
use Cundd\Stairtower\Filter\Comparison\LogicalComparison;
use Cundd\Stairtower\Filter\Comparison\PropertyComparison;
use Cundd\Stairtower\Filter\Filter;
use Cundd\Stairtower\Filter\FilterResult;
use Cundd\Stairtower\Tests\Unit\AbstractDatabaseBasedCase;

/**
 * Test for Cundd\Stairtower\Filter\FilterResult
 */
class FilterResultTest extends AbstractDatabaseBasedCase
{
    /**
     * @var \Cundd\Stairtower\Filter\FilterResult
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
        /** @var \Cundd\Stairtower\DataAccess\Coordinator $coordinator */
        $coordinator = $this->getDiContainer()->get(Coordinator::class);

        $filter = new Filter(
            new LogicalComparison(
                ComparisonInterface::TYPE_AND,
                new PropertyComparison('eyeColor', ComparisonInterface::TYPE_EQUAL_TO, 'green'),
                new PropertyComparison('name', ComparisonInterface::TYPE_EQUAL_TO, 'Booker Oneil')
            )
        );

        $database = $coordinator->getDatabase('people');
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
        $filter = new Filter(
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
        $this->assertInstanceOf(FilterResult::class, $filterResult);
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


        $filter = new Filter(
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
        $this->assertInstanceOf(FilterResult::class, $filterResult);
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


        $filter = new Filter(
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
        $this->assertInstanceOf(FilterResult::class, $filterResult);
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
        $this->assertEquals(24, $this->fixture->count());
        $this->assertNotNull($this->fixture->current());

        iterator_to_array($this->fixture);
        $this->assertEquals(24, $this->fixture->count());
    }

    /**
     * @test
     * @expectedException \Cundd\Stairtower\Core\ArrayException\IndexOutOfRangeException
     */
    public function iterateAndGetCurrentShouldThrowAnException()
    {
        $this->assertEquals(24, $this->fixture->count());
        $this->assertNotNull($this->fixture->current());

        iterator_to_array($this->fixture);
        $this->assertEquals(24, $this->fixture->count());
        $this->assertNotNull($this->fixture->current());
    }

    /**
     * @test
     */
    public function doALotOfThings()
    {
        $this->assertEquals(24, $this->fixture->count());
        $this->assertNotNull($this->fixture->current());

        iterator_to_array($this->fixture);
        $this->assertEquals(24, $this->fixture->count());

        $this->fixture->rewind();
        $this->fixture->next();
        iterator_to_array($this->fixture);
        $this->fixture->rewind();

        $i = 0;
        while (++$i < $this->fixture->count()) {
            $this->fixture->next();
        }
    }

    /**
     * A test that should validate the behavior of data object references in a database
     *
     * @test
     */
    public function objectLiveCycleTest()
    {
        $database = $this->getSmallPeopleDatabase();
        $newFilter = new Filter(new PropertyComparison('eyeColor', ComparisonInterface::TYPE_EQUAL_TO, 'green'));
        $newFilterResult = $newFilter->filterCollection($database);

        /** @var DocumentInterface $memberFromNewFilter */
        $memberFromNewFilter = $newFilterResult->current();

        /** @var DocumentInterface $memberFromFixture */
        $memberFromFixture = $this->fixture->current();

        $this->assertEquals($memberFromNewFilter, $memberFromFixture);

        $movie = 'Star Wars';
        $key = 'favorite_movie';

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
        $exampleCollection->attach(
            new Document(
                [
                    'name'    => 'Red Hot Chili Peppers',
                    'founded' => 1983,
                    'breakUp' => null,
                    'url'     => 'http://www.redhotchilipeppers.com/',
                ]
            )
        );

        $exampleCollection->attach(
            new Document(
                [
                    'name'    => 'The Beatles',
                    'founded' => 1960,
                    'breakUp' => 1970,
                    'url'     => 'http://thebeatles.com/',
                ]
            )
        );

        $exampleCollection->attach(
            new Document(
                [
                    'name'    => 'Pink Floyd',
                    'founded' => 1965,
                    'breakUp' => 2014,
                    'url'     => 'http://thebeatles.com/',
                ]
            )
        );

        $filter = new Filter(
            new PropertyComparison(
                'breakUp', ComparisonInterface::TYPE_LESS_THAN_OR_EQUAL_TO,
                2014
            )
        );
        $filterResult = $filter->filterCollection($exampleCollection);

        $this->assertEquals(3, $filterResult->count());


        $exampleCollection->attach(
            new Document(
                [
                    'name'    => 'Starflyer 59',
                    'founded' => 1993,
                    'breakUp' => null,
                    'url'     => 'http://www.sf59.com/',
                ]
            )
        );

        $this->assertEquals(3, $filterResult->count());
    }

    protected function setUp()
    {
        $this->setUpXhprof();

        $database = $this->getSmallPeopleDatabase();
        $this->filter = new Filter();
        $this->filter->setComparison(new PropertyComparison('eyeColor', ComparisonInterface::TYPE_EQUAL_TO, 'green'));
        $this->fixture = $this->filter->filterCollection($database);
    }

    protected function tearDown()
    {
        unset($this->filter);
        unset($this->fixture);
        parent::tearDown();
    }
}
 