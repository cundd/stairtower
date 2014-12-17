<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 31.08.14
 * Time: 16:13
 */

namespace Cundd\PersistentObjectStore\Filter;

use Cundd\PersistentObjectStore\AbstractDatabaseBasedCase;
use Cundd\PersistentObjectStore\Domain\Model\DatabaseInterface;
use Cundd\PersistentObjectStore\Filter\Comparison\ComparisonInterface;

/**
 * Test for \Cundd\PersistentObjectStore\Filter\FilterBuilderInterface
 *
 * @package Cundd\PersistentObjectStore\FilterBuilderInterface
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
        $query        = array('name' => 'Daniel Corn');
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


        $query        = array('eyeColor' => 'green');
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
        $query        = array('name' => 'Daniel Corn', 'eyeColor' => 'brown');
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


        $query        = array('eyeColor' => 'green', 'email' => 'nolanbyrd@vantage.com');
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


        $query        = array(
            ComparisonInterface::TYPE_AND => array(
                ['eyeColor' => 'brown'],
                ['name' => 'Daniel Corn'],
            )
        );
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


        $query        = array(
            '$and' => array(
                ['name' => 'Nolan Byrd'],
                ['eyeColor' => 'green'],
            )
        );
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
        $query        = array(
            ComparisonInterface::TYPE_OR => array(
                ['email' => 'spm@cundd.net'],
                ['email' => 'nolanbyrd@vantage.com']
            )
        );
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


        $query        = array(
            '$or' => array(
                ['email' => 'spm@cundd.net'],
                ['email' => 'nolanbyrd@vantage.com']
            )
        );
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
        $query        = array('name' => array(ComparisonInterface::TYPE_EQUAL_TO => 'Daniel Corn'));
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


        $query        = array('name' => array('$eq' => 'Daniel Corn'));
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
        $query        = array('name' => array(ComparisonInterface::TYPE_NOT_EQUAL_TO => 'Daniel Corn'));
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


        $query        = array('name' => array(ComparisonInterface::TYPE_NOT_EQUAL_TO => 'Daniel Corn'));
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
        $query        = array('age' => array(ComparisonInterface::TYPE_LESS_THAN => 25));
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


        $query        = array('age' => array('$lt' => 25));
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
        $query        = array('age' => array(ComparisonInterface::TYPE_LESS_THAN_OR_EQUAL_TO => 25));
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


        $query        = array('age' => array('$lte' => 25));
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
        $query        = array('age' => array(ComparisonInterface::TYPE_GREATER_THAN => 25));
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


        $query        = array('age' => array('$gt' => 25));
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
        $query        = array('age' => array(ComparisonInterface::TYPE_GREATER_THAN_OR_EQUAL_TO => 25));
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


        $query        = array('age' => array('$gte' => 25));
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
        $query        = array('name' => array(ComparisonInterface::TYPE_LIKE => 'Daniel Corn'));
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


        $query        = array('name' => array('$lk' => 'Daniel Corn'));
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
        $query        = array('name' => array(ComparisonInterface::TYPE_CONTAINS => 'niel Co'));
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


        $query        = array('name' => array('$con' => 'niel Co'));
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
        $query        = array('name' => array(ComparisonInterface::TYPE_IN => 'Daniel Corn'));
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


        $query        = array('name' => array('$in' => 'Daniel Corn'));
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
        $query        = array('name' => ComparisonInterface::TYPE_IS_NULL);
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertEquals(0, $filterResult->count());


        $query        = array('not-existing-' . time() => ComparisonInterface::TYPE_IS_NULL);
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


        $query        = array('name' => '$null');
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertEquals(0, $filterResult->count());


        $query        = array('not-existing-' . time() => '$null');
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
        $query        = array('name' => ComparisonInterface::TYPE_IS_EMPTY);
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertEquals(0, $filterResult->count());

        $query        = array('not-existing-' . time() => ComparisonInterface::TYPE_IS_EMPTY);
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


        $query        = array('name' => '$em');
        $filter = $this->fixture->buildFilter($query);
        $filterResult = $filter->filterCollection($this->database);
        $this->assertInstanceOf('Cundd\\PersistentObjectStore\\Filter\\FilterResult', $filterResult);
        $this->assertEquals(0, $filterResult->count());

        $query        = array('not-existing-' . time() => '$em');
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
        $query        = array(
            ComparisonInterface::TYPE_OR => array(
                array(
                    'eyeColor' => 'blue',
                    'age'      => array(ComparisonInterface::TYPE_LESS_THAN => 25),
                ),
                array(
                    'eyeColor' => 'green',
                    'age'      => array(ComparisonInterface::TYPE_LESS_THAN => 25),
                ),
            ),
        );
        $filter       = $this->fixture->buildFilter($query);
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


        $query        = array(
            ComparisonInterface::TYPE_OR => array(
                array(
                    ComparisonInterface::TYPE_AND => array(
                        'eyeColor' => 'blue',
                        'age'      => array(ComparisonInterface::TYPE_LESS_THAN => 25),
                    ),
                ),
                array(
                    ComparisonInterface::TYPE_AND => array(
                        'eyeColor' => 'green',
                        'age'      => array(ComparisonInterface::TYPE_LESS_THAN => 25),
                    ),
                ),
            ),
        );
        $filter       = $this->fixture->buildFilter($query);
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


        $query        = array(
            ComparisonInterface::TYPE_AND => array(
                ComparisonInterface::TYPE_OR => array(
                    ['eyeColor' => 'blue'], // 14
                    ['eyeColor' => 'green'], // 10
                ),
                'age'                        => array(ComparisonInterface::TYPE_LESS_THAN => 25),
            ),
        );
        $filter       = $this->fixture->buildFilter($query);
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


        $query        = array(
            ComparisonInterface::TYPE_OR => array(
                ['eyeColor' => 'blue'],
                ['eyeColor' => 'green']
            ),
            'age'                        => array(ComparisonInterface::TYPE_LESS_THAN => 25),
        );
        $filter       = $this->fixture->buildFilter($query);
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


        $query        = array(
            '$or' => array(
                ['eyeColor' => 'blue'],
                ['eyeColor' => 'green']
            ),
            'age' => array('$lt' => 25),
        );
        $filter       = $this->fixture->buildFilter($query);
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
 