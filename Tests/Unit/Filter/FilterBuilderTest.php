<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Filter;

use Cundd\PersistentObjectStore\AbstractDatabaseBasedCase;
use Cundd\PersistentObjectStore\Domain\Model\DatabaseInterface;
use Cundd\PersistentObjectStore\Filter\Comparison\ComparisonInterface;

/**
 * Test for \Cundd\PersistentObjectStore\Filter\FilterBuilderInterface
 */
class FilterBuilderTest extends AbstractDatabaseBasedCase
{
    /**
     * @var \Cundd\PersistentObjectStore\Filter\FilterBuilderInterface
     */
    protected $fixture;

    /**
     * @var DatabaseInterface
     */
    protected $database;

    /**
     * @test
     */
    public function simpleSearchTest()
    {
        $query = ['name' => 'Daniel Corn'];
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertEquals(1, $filterResult->count());

        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertSame('spm@cundd.net', $currentObject->valueForKey('email'));
        $this->assertSame('Daniel Corn', $currentObject->valueForKey('name'));
        $this->assertContains('developer', $currentObject->valueForKey('tags'));
        $this->assertContains('brown', $currentObject->valueForKey('eyeColor'));


        $query = ['eyeColor' => 'green'];
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertEquals(24, $filterResult->count());

        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertSame('nolanbyrd@vantage.com', $currentObject->valueForKey('email'));
        $this->assertSame('Nolan Byrd', $currentObject->valueForKey('name'));
        $this->assertContains('labore', $currentObject->valueForKey('tags'));
        $this->assertContains('green', $currentObject->valueForKey('eyeColor'));
    }

    /**
     * @test
     */
    public function simpleAndSearchTest()
    {
        $query = ['name' => 'Daniel Corn', 'eyeColor' => 'brown'];
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertEquals(1, $filterResult->count());

        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertSame('spm@cundd.net', $currentObject->valueForKey('email'));
        $this->assertSame('Daniel Corn', $currentObject->valueForKey('name'));
        $this->assertContains('developer', $currentObject->valueForKey('tags'));
        $this->assertContains('brown', $currentObject->valueForKey('eyeColor'));


        $query = ['eyeColor' => 'green', 'email' => 'nolanbyrd@vantage.com'];
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertEquals(1, $filterResult->count());

        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertSame('nolanbyrd@vantage.com', $currentObject->valueForKey('email'));
        $this->assertSame('Nolan Byrd', $currentObject->valueForKey('name'));
        $this->assertContains('labore', $currentObject->valueForKey('tags'));
        $this->assertContains('green', $currentObject->valueForKey('eyeColor'));


        $query = [
            ComparisonInterface::TYPE_AND => [
                ['eyeColor' => 'brown'],
                ['name' => 'Daniel Corn'],
            ],
        ];
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertEquals(1, $filterResult->count());

        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertSame('spm@cundd.net', $currentObject->valueForKey('email'));
        $this->assertSame('Daniel Corn', $currentObject->valueForKey('name'));
        $this->assertContains('developer', $currentObject->valueForKey('tags'));
        $this->assertContains('brown', $currentObject->valueForKey('eyeColor'));


        $query = [
            '$and' => [
                ['name' => 'Nolan Byrd'],
                ['eyeColor' => 'green'],
            ],
        ];
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertEquals(1, $filterResult->count());

        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertSame('nolanbyrd@vantage.com', $currentObject->valueForKey('email'));
        $this->assertSame('Nolan Byrd', $currentObject->valueForKey('name'));
        $this->assertContains('labore', $currentObject->valueForKey('tags'));
        $this->assertContains('green', $currentObject->valueForKey('eyeColor'));
    }

    /**
     * @test
     */
    public function simpleOrSearchTest()
    {
        $query = [
            ComparisonInterface::TYPE_OR => [
                ['email' => 'spm@cundd.net'],
                ['email' => 'nolanbyrd@vantage.com'],
            ],
        ];
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertEquals(2, $filterResult->count());

        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertSame('spm@cundd.net', $currentObject->valueForKey('email'));
        $this->assertSame('Daniel Corn', $currentObject->valueForKey('name'));
        $this->assertContains('developer', $currentObject->valueForKey('tags'));
        $this->assertContains('brown', $currentObject->valueForKey('eyeColor'));

        $filterResult->next();
        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertSame('nolanbyrd@vantage.com', $currentObject->valueForKey('email'));
        $this->assertSame('Nolan Byrd', $currentObject->valueForKey('name'));
        $this->assertContains('labore', $currentObject->valueForKey('tags'));
        $this->assertContains('green', $currentObject->valueForKey('eyeColor'));


        $query = [
            '$or' => [
                ['email' => 'spm@cundd.net'],
                ['email' => 'nolanbyrd@vantage.com'],
            ],
        ];
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertEquals(2, $filterResult->count());

        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertSame('spm@cundd.net', $currentObject->valueForKey('email'));
        $this->assertSame('Daniel Corn', $currentObject->valueForKey('name'));
        $this->assertContains('developer', $currentObject->valueForKey('tags'));
        $this->assertContains('brown', $currentObject->valueForKey('eyeColor'));

        $filterResult->next();
        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertSame('nolanbyrd@vantage.com', $currentObject->valueForKey('email'));
        $this->assertSame('Nolan Byrd', $currentObject->valueForKey('name'));
        $this->assertContains('labore', $currentObject->valueForKey('tags'));
        $this->assertContains('green', $currentObject->valueForKey('eyeColor'));
    }

    /**
     * @test
     */
    public function searchTypeEqualToTest()
    {
        $query = ['name' => [ComparisonInterface::TYPE_EQUAL_TO => 'Daniel Corn']];
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertEquals(1, $filterResult->count());

        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertSame('spm@cundd.net', $currentObject->valueForKey('email'));
        $this->assertSame('Daniel Corn', $currentObject->valueForKey('name'));
        $this->assertContains('developer', $currentObject->valueForKey('tags'));
        $this->assertContains('brown', $currentObject->valueForKey('eyeColor'));


        $query = ['name' => ['$eq' => 'Daniel Corn']];
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertEquals(1, $filterResult->count());

        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertSame('spm@cundd.net', $currentObject->valueForKey('email'));
        $this->assertSame('Daniel Corn', $currentObject->valueForKey('name'));
        $this->assertContains('developer', $currentObject->valueForKey('tags'));
        $this->assertContains('brown', $currentObject->valueForKey('eyeColor'));
    }

    /**
     * @test
     */
    public function searchTypeNotEqualToTest()
    {
        $query = ['name' => [ComparisonInterface::TYPE_NOT_EQUAL_TO => 'Daniel Corn']];
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertEquals(92, $filterResult->count());

        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertSame('nolanbyrd@vantage.com', $currentObject->valueForKey('email'));
        $this->assertSame('Nolan Byrd', $currentObject->valueForKey('name'));
        $this->assertContains('labore', $currentObject->valueForKey('tags'));
        $this->assertContains('green', $currentObject->valueForKey('eyeColor'));


        $query = ['name' => [ComparisonInterface::TYPE_NOT_EQUAL_TO => 'Daniel Corn']];
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertEquals(92, $filterResult->count());

        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertSame('nolanbyrd@vantage.com', $currentObject->valueForKey('email'));
        $this->assertSame('Nolan Byrd', $currentObject->valueForKey('name'));
        $this->assertContains('labore', $currentObject->valueForKey('tags'));
        $this->assertContains('green', $currentObject->valueForKey('eyeColor'));
    }

    /**
     * @test
     */
    public function searchTypeLessThanTest()
    {
        $query = ['age' => [ComparisonInterface::TYPE_LESS_THAN => 25]];
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertEquals(32, $filterResult->count());

        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertSame('angelaroberts@cundd.net', $currentObject->valueForKey('email'));
        $this->assertSame('Angela Roberts', $currentObject->valueForKey('name'));
        $this->assertContains('eu', $currentObject->valueForKey('tags'));
        $this->assertContains('blue', $currentObject->valueForKey('eyeColor'));


        $query = ['age' => ['$lt' => 25]];
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertEquals(32, $filterResult->count());

        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertSame('angelaroberts@cundd.net', $currentObject->valueForKey('email'));
        $this->assertSame('Angela Roberts', $currentObject->valueForKey('name'));
        $this->assertContains('eu', $currentObject->valueForKey('tags'));
        $this->assertContains('blue', $currentObject->valueForKey('eyeColor'));
    }

    /**
     * @test
     */
    public function searchTypeLessThanOrEqualToTest()
    {
        $query = ['age' => [ComparisonInterface::TYPE_LESS_THAN_OR_EQUAL_TO => 25]];
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertEquals(37, $filterResult->count());

        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertSame('angelaroberts@cundd.net', $currentObject->valueForKey('email'));
        $this->assertSame('Angela Roberts', $currentObject->valueForKey('name'));
        $this->assertContains('eu', $currentObject->valueForKey('tags'));
        $this->assertContains('blue', $currentObject->valueForKey('eyeColor'));


        $query = ['age' => ['$lte' => 25]];
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertEquals(37, $filterResult->count());

        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertSame('angelaroberts@cundd.net', $currentObject->valueForKey('email'));
        $this->assertSame('Angela Roberts', $currentObject->valueForKey('name'));
        $this->assertContains('eu', $currentObject->valueForKey('tags'));
        $this->assertContains('blue', $currentObject->valueForKey('eyeColor'));
    }

    /**
     * @test
     */
    public function searchTypeGreaterThanTest()
    {
        $query = ['age' => [ComparisonInterface::TYPE_GREATER_THAN => 25]];
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertEquals(56, $filterResult->count());

        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertSame('spm@cundd.net', $currentObject->valueForKey('email'));
        $this->assertSame('Daniel Corn', $currentObject->valueForKey('name'));
        $this->assertContains('developer', $currentObject->valueForKey('tags'));
        $this->assertContains('brown', $currentObject->valueForKey('eyeColor'));


        $query = ['age' => ['$gt' => 25]];
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertEquals(56, $filterResult->count());

        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertSame('spm@cundd.net', $currentObject->valueForKey('email'));
        $this->assertSame('Daniel Corn', $currentObject->valueForKey('name'));
        $this->assertContains('developer', $currentObject->valueForKey('tags'));
        $this->assertContains('brown', $currentObject->valueForKey('eyeColor'));
    }

    /**
     * @test
     */
    public function searchTypeGreaterThanOrEqualToTest()
    {
        $query = ['age' => [ComparisonInterface::TYPE_GREATER_THAN_OR_EQUAL_TO => 25]];
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertEquals(61, $filterResult->count());

        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertSame('spm@cundd.net', $currentObject->valueForKey('email'));
        $this->assertSame('Daniel Corn', $currentObject->valueForKey('name'));
        $this->assertContains('developer', $currentObject->valueForKey('tags'));
        $this->assertContains('brown', $currentObject->valueForKey('eyeColor'));


        $query = ['age' => ['$gte' => 25]];
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertEquals(61, $filterResult->count());

        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertSame('spm@cundd.net', $currentObject->valueForKey('email'));
        $this->assertSame('Daniel Corn', $currentObject->valueForKey('name'));
        $this->assertContains('developer', $currentObject->valueForKey('tags'));
        $this->assertContains('brown', $currentObject->valueForKey('eyeColor'));
    }

    /**
     * @test
     */
    public function searchTypeLikeTest()
    {
        $query = ['name' => [ComparisonInterface::TYPE_LIKE => 'Daniel Corn']];
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertEquals(1, $filterResult->count());

        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertSame('spm@cundd.net', $currentObject->valueForKey('email'));
        $this->assertSame('Daniel Corn', $currentObject->valueForKey('name'));
        $this->assertContains('developer', $currentObject->valueForKey('tags'));
        $this->assertContains('brown', $currentObject->valueForKey('eyeColor'));


        $query = ['name' => ['$lk' => 'Daniel Corn']];
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertEquals(1, $filterResult->count());

        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertSame('spm@cundd.net', $currentObject->valueForKey('email'));
        $this->assertSame('Daniel Corn', $currentObject->valueForKey('name'));
        $this->assertContains('developer', $currentObject->valueForKey('tags'));
        $this->assertContains('brown', $currentObject->valueForKey('eyeColor'));
    }

    /**
     * @test
     */
    public function searchTypeContainsTest()
    {
        $query = ['name' => [ComparisonInterface::TYPE_CONTAINS => 'niel Co']];
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertEquals(1, $filterResult->count());

        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertSame('spm@cundd.net', $currentObject->valueForKey('email'));
        $this->assertSame('Daniel Corn', $currentObject->valueForKey('name'));
        $this->assertContains('developer', $currentObject->valueForKey('tags'));
        $this->assertContains('brown', $currentObject->valueForKey('eyeColor'));


        $query = ['name' => ['$con' => 'niel Co']];
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertEquals(1, $filterResult->count());

        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertSame('spm@cundd.net', $currentObject->valueForKey('email'));
        $this->assertSame('Daniel Corn', $currentObject->valueForKey('name'));
        $this->assertContains('developer', $currentObject->valueForKey('tags'));
        $this->assertContains('brown', $currentObject->valueForKey('eyeColor'));
    }

    /**
     * @test
     */
    public function searchTypeInTest()
    {
        $query = ['name' => [ComparisonInterface::TYPE_IN => 'Daniel Corn']];
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertEquals(1, $filterResult->count());

        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertSame('spm@cundd.net', $currentObject->valueForKey('email'));
        $this->assertSame('Daniel Corn', $currentObject->valueForKey('name'));
        $this->assertContains('developer', $currentObject->valueForKey('tags'));
        $this->assertContains('brown', $currentObject->valueForKey('eyeColor'));


        $query = ['name' => ['$in' => 'Daniel Corn']];
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertEquals(1, $filterResult->count());

        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertSame('spm@cundd.net', $currentObject->valueForKey('email'));
        $this->assertSame('Daniel Corn', $currentObject->valueForKey('name'));
        $this->assertContains('developer', $currentObject->valueForKey('tags'));
        $this->assertContains('brown', $currentObject->valueForKey('eyeColor'));
    }

    /**
     * @test
     */
    public function searchTypeIsNullTest()
    {
        $query = ['name' => ComparisonInterface::TYPE_IS_NULL];
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertEquals(0, $filterResult->count());


        $query = ['not-existing-' . time() => ComparisonInterface::TYPE_IS_NULL];
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertEquals(93, $filterResult->count());

        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertSame('spm@cundd.net', $currentObject->valueForKey('email'));
        $this->assertSame('Daniel Corn', $currentObject->valueForKey('name'));
        $this->assertContains('developer', $currentObject->valueForKey('tags'));
        $this->assertContains('brown', $currentObject->valueForKey('eyeColor'));


        $query = ['name' => '$null'];
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertEquals(0, $filterResult->count());


        $query = ['not-existing-' . time() => '$null'];
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertEquals(93, $filterResult->count());

        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertSame('spm@cundd.net', $currentObject->valueForKey('email'));
        $this->assertSame('Daniel Corn', $currentObject->valueForKey('name'));
        $this->assertContains('developer', $currentObject->valueForKey('tags'));
        $this->assertContains('brown', $currentObject->valueForKey('eyeColor'));
    }

    /**
     * @test
     */
    public function searchTypeIsEmptyTest()
    {
        $query = ['name' => ComparisonInterface::TYPE_IS_EMPTY];
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertEquals(0, $filterResult->count());

        $query = ['not-existing-' . time() => ComparisonInterface::TYPE_IS_EMPTY];
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertEquals(93, $filterResult->count());

        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertSame('spm@cundd.net', $currentObject->valueForKey('email'));
        $this->assertSame('Daniel Corn', $currentObject->valueForKey('name'));
        $this->assertContains('developer', $currentObject->valueForKey('tags'));
        $this->assertContains('brown', $currentObject->valueForKey('eyeColor'));


        $query = ['name' => '$em'];
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertEquals(0, $filterResult->count());

        $query = ['not-existing-' . time() => '$em'];
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertEquals(93, $filterResult->count());

        $currentObject = $filterResult->current();
        $this->assertNotNull($currentObject);

        $this->assertSame('spm@cundd.net', $currentObject->valueForKey('email'));
        $this->assertSame('Daniel Corn', $currentObject->valueForKey('name'));
        $this->assertContains('developer', $currentObject->valueForKey('tags'));
        $this->assertContains('brown', $currentObject->valueForKey('eyeColor'));
    }

    /**
     * @test
     */
    public function advancedSearchTest()
    {
        $query = [
            ComparisonInterface::TYPE_OR => [
                [
                    'eyeColor' => 'blue',
                    'age'      => [ComparisonInterface::TYPE_LESS_THAN => 25],
                ],
                [
                    'eyeColor' => 'green',
                    'age'      => [ComparisonInterface::TYPE_LESS_THAN => 25],
                ],
            ],
        ];
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);

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


        $query = [
            ComparisonInterface::TYPE_OR => [
                [
                    ComparisonInterface::TYPE_AND => [
                        'eyeColor' => 'blue',
                        'age'      => [ComparisonInterface::TYPE_LESS_THAN => 25],
                    ],
                ],
                [
                    ComparisonInterface::TYPE_AND => [
                        'eyeColor' => 'green',
                        'age'      => [ComparisonInterface::TYPE_LESS_THAN => 25],
                    ],
                ],
            ],
        ];
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);

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


        $query = [
            ComparisonInterface::TYPE_AND => [
                ComparisonInterface::TYPE_OR => [
                    ['eyeColor' => 'blue'], // 14
                    ['eyeColor' => 'green'], // 10
                ],
                'age'                        => [ComparisonInterface::TYPE_LESS_THAN => 25],
            ],
        ];
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);
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


        $query = [
            ComparisonInterface::TYPE_OR => [
                ['eyeColor' => 'blue'],
                ['eyeColor' => 'green'],
            ],
            'age'                        => [ComparisonInterface::TYPE_LESS_THAN => 25],
        ];
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);
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


        $query = [
            '$or' => [
                ['eyeColor' => 'blue'],
                ['eyeColor' => 'green'],
            ],
            'age' => ['$lt' => 25],
        ];
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);
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

    protected function setUp()
    {
        parent::setUp();
        $this->setUpXhprof();
        $this->database = $this->getSmallPeopleDatabase();
    }

    protected function tearDown()
    {
        unset($this->fixture);
        unset($this->database);
        parent::tearDown();
    }


}
 