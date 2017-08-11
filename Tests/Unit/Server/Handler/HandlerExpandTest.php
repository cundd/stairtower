<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Tests\Unit\Server\Handler;


use Cundd\Stairtower\Asset\AssetProviderInterface;
use Cundd\Stairtower\Constants;
use Cundd\Stairtower\DataAccess\CoordinatorInterface;
use Cundd\Stairtower\Domain\Model\DatabaseInterface;
use Cundd\Stairtower\Domain\Model\DocumentInterface;
use Cundd\Stairtower\Event\SharedEventEmitter;
use Cundd\Stairtower\Expand\ExpandConfigurationBuilderInterface;
use Cundd\Stairtower\Expand\ExpandResolverInterface;
use Cundd\Stairtower\Filter\FilterBuilderInterface;
use Cundd\Stairtower\Filter\FilterResultInterface;
use Cundd\Stairtower\Memory\Manager;
use Cundd\Stairtower\Server\Handler\Handler;
use Cundd\Stairtower\Server\Handler\HandlerInterface;
use Cundd\Stairtower\Server\Handler\HandlerResultInterface;
use Cundd\Stairtower\Server\ServerInterface;
use Cundd\Stairtower\Server\ValueObject\RequestInfoFactory;
use Cundd\Stairtower\Tests\Unit\AbstractCase;
use Cundd\Stairtower\Tests\Unit\RequestBuilderTrait;
use SplFixedArray;

/**
 * Handler test
 */
class HandlerExpandTest extends AbstractCase
{
    use RequestBuilderTrait;
    /**
     * @var HandlerInterface
     */
    protected $fixture;

    /**
     * @var DatabaseInterface
     */
    protected $database;

    /**
     * @var RequestInfoFactory
     */
    protected $requestInfoFactory;

    /**
     * @test
     */
    public function readDatabaseWithExpandTest()
    {
        // Query '$expand=person/contacts/email'
        $queryString = vsprintf(
            '%s=person%scontacts%semail',
            [
                Constants::EXPAND_KEYWORD,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
            ]
        );
        parse_str($queryString, $query);
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest('GET', '/loaned/', $query)
        );
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            HandlerResultInterface::class,
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());

        $this->assertInstanceOf(SplFixedArray::class, $handlerResult->getData());
        $this->assertEquals(4, $handlerResult->getData()->count());

        /** @var DocumentInterface $dataInstance */
        $dataInstance = $handlerResult->getData()->current();
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person.email'));
        $handlerResult->getData()->next();
        $handlerResult->getData()->next();
        $dataInstance = $handlerResult->getData()->current();
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person.email'));
    }

    /**
     * @test
     */
    public function readDatabaseWithExpandIdTest()
    {
        // Query '$expand=person/contacts/email'
        $queryString = vsprintf(
            '%s=person%scontacts%s%s',
            [
                Constants::EXPAND_KEYWORD,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
                Constants::DATA_ID_KEY,
            ]
        );
        parse_str($queryString, $query);
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest('GET', '/loaned/', $query)
        );
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            HandlerResultInterface::class,
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());

        $this->assertInstanceOf(SplFixedArray::class, $handlerResult->getData());
        $this->assertEquals(4, $handlerResult->getData()->count());

        /** @var DocumentInterface $dataInstance */
        $dataInstance = $handlerResult->getData()->current();
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person.email'));
        $handlerResult->getData()->next();
        $handlerResult->getData()->next();
        $dataInstance = $handlerResult->getData()->current();
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person.email'));
    }

    /**
     * @test
     */
    public function readDatabaseWithMoreThanOneExpandsTest()
    {
        // Query '$expand=person/contacts/email/-/book/book/isbn_10'
        $queryString = vsprintf(
            '%s=person%scontacts%semail%sbook%sbook%sisbn_10',
            [
                Constants::EXPAND_KEYWORD,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
                Constants::EXPAND_REQUEST_DELIMITER,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
            ]
        );
        parse_str($queryString, $query);
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest('GET', '/loaned/', $query)
        );
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            HandlerResultInterface::class,
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());

        $this->assertInstanceOf(SplFixedArray::class, $handlerResult->getData());
        $this->assertEquals(4, $handlerResult->getData()->count());

        /** @var DocumentInterface $dataInstance */
        $dataInstance = $handlerResult->getData()->current();
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person.email'));
        $this->assertEquals('0395595118', $dataInstance->valueForKeyPath('book.isbn_10'));
        $this->assertEquals('The Lord of the rings', $dataInstance->valueForKeyPath('book.title'));
        $handlerResult->getData()->next();
        $handlerResult->getData()->next();
        $dataInstance = $handlerResult->getData()->current();
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person.email'));
        $this->assertEquals('0345253426', $dataInstance->valueForKeyPath('book.isbn_10'));
        $this->assertEquals('The Hobbit', $dataInstance->valueForKeyPath('book.title'));
    }

    /**
     * @test
     */
    public function readDocumentWithExpandTest()
    {
        // Query '$expand=person/contacts/email'
        $queryString = vsprintf(
            '%s=person%scontacts%semail',
            [
                Constants::EXPAND_KEYWORD,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
            ]
        );
        parse_str($queryString, $query);
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'GET',
                '/loaned/L1420194884',
                $query
            )
        );
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            HandlerResultInterface::class,
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());
        $this->assertInstanceOf(
            DocumentInterface::class,
            $handlerResult->getData()
        );

        /** @var DocumentInterface $dataInstance */
        $dataInstance = $handlerResult->getData();
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person.email'));
    }

    /**
     * @test
     */
    public function readDocumentWithExpandIdTest()
    {
        // Query '$expand=person/contacts/email'
        $queryString = vsprintf(
            '%s=person%scontacts%s%s',
            [
                Constants::EXPAND_KEYWORD,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
                Constants::DATA_ID_KEY,
            ]
        );
        parse_str($queryString, $query);
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'GET',
                '/loaned/L1420194884',
                $query
            )
        );
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            HandlerResultInterface::class,
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());
        $this->assertInstanceOf(
            DocumentInterface::class,
            $handlerResult->getData()
        );

        /** @var DocumentInterface $dataInstance */
        $dataInstance = $handlerResult->getData();
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person.email'));
    }

    /**
     * @test
     */
    public function readDocumentWithMoreThanOneExpandsTest()
    {
        // Query '$expand=person/contacts/email/-/book/book/isbn_10'
        $queryString = vsprintf(
            '%s=person%scontacts%semail%sbook%sbook%sisbn_10',
            [
                Constants::EXPAND_KEYWORD,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
                Constants::EXPAND_REQUEST_DELIMITER,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
            ]
        );
        parse_str($queryString, $query);
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'GET',
                '/loaned/L1420194884',
                $query
            )
        );
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            HandlerResultInterface::class,
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());
        $this->assertInstanceOf(
            DocumentInterface::class,
            $handlerResult->getData()
        );

        /** @var DocumentInterface $dataInstance */
        $dataInstance = $handlerResult->getData();
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person.email'));
        $this->assertEquals('0345253426', $dataInstance->valueForKeyPath('book.isbn_10'));
        $this->assertEquals('The Hobbit', $dataInstance->valueForKeyPath('book.title'));
    }

    /**
     * @test
     */
    public function readWithSearchAndExpandTest()
    {
        // Query 'title=The Hobbit&$expand=person/contacts/email'
        $queryString = vsprintf(
            'title=The Hobbit&%s=person%scontacts%semail',
            [
                Constants::EXPAND_KEYWORD,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
            ]
        );
        parse_str($queryString, $query);
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest('GET', '/loaned/', $query)
        );
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            HandlerResultInterface::class,
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());

        $this->assertInstanceOf(SplFixedArray::class, $handlerResult->getData());
        $this->assertEquals(1, $handlerResult->getData()->count());

        /** @var DocumentInterface $dataInstance */
        $dataInstance = $handlerResult->getData()->current();
        $this->assertEquals('The Hobbit', $dataInstance->valueForKeyPath('title'));
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person.email'));


        // Query '$expand=person/contacts/email&title=The Hobbit'
        $queryString = vsprintf(
            '%s=person%scontacts%semail&title=The Hobbit',
            [
                Constants::EXPAND_KEYWORD,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
            ]
        );
        parse_str($queryString, $query);
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest('GET', '/loaned/', $query)
        );
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            HandlerResultInterface::class,
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());

        $this->assertInstanceOf(SplFixedArray::class, $handlerResult->getData());
        $this->assertEquals(1, $handlerResult->getData()->count());

        /** @var DocumentInterface $dataInstance */
        $dataInstance = $handlerResult->getData()->current();
        $this->assertEquals('The Hobbit', $dataInstance->valueForKeyPath('title'));
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person.email'));
    }

    /**
     * @test
     */
    public function readWithEmptyResultSearchAndExpandTest()
    {
        // Query 'firstName=Some-thing-not-existing&$expand=person/contacts/email'
        $queryString = vsprintf(
            'firstName=Some-thing-not-existing&%s=person%scontacts%semail',
            [
                Constants::EXPAND_KEYWORD,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
            ]
        );
        parse_str($queryString, $query);
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest('GET', '/contacts/', $query)
        );
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            HandlerResultInterface::class,
            $handlerResult
        );
        $this->assertEquals(404, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());
        $this->assertInstanceOf(
            FilterResultInterface::class,
            $handlerResult->getData()
        );
        $this->assertEquals(0, $handlerResult->getData()->count());


        // Query 'some-thing-not-existing=Daniel&$expand=person/contacts/email'
        $queryString = vsprintf(
            'some-thing-not-existing=Daniel&%s=person%scontacts%semail',
            [
                Constants::EXPAND_KEYWORD,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
            ]
        );
        parse_str($queryString, $query);
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest('GET', '/contacts/', $query)
        );
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            HandlerResultInterface::class,
            $handlerResult
        );
        $this->assertEquals(404, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());
        $this->assertInstanceOf(
            FilterResultInterface::class,
            $handlerResult->getData()
        );
        $this->assertEquals(0, $handlerResult->getData()->count());
    }


    /**
     * @test
     */
    public function readDatabaseWithExpandAndAsPropertyTest()
    {
        // Query '$expand=person/contacts/email'
        $queryString = vsprintf(
            '%s=person%scontacts%semail%sperson-data',
            [
                Constants::EXPAND_KEYWORD,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
            ]
        );
        parse_str($queryString, $query);
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest('GET', '/loaned/', $query)
        );
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            HandlerResultInterface::class,
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());

        $this->assertInstanceOf(SplFixedArray::class, $handlerResult->getData());
        $this->assertEquals(4, $handlerResult->getData()->count());

        /** @var DocumentInterface $dataInstance */
        $dataInstance = $handlerResult->getData()->current();
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person'));
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person-data.email'));
        $handlerResult->getData()->next();
        $handlerResult->getData()->next();
        $dataInstance = $handlerResult->getData()->current();
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person'));
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person-data.email'));
    }

    /**
     * @test
     */
    public function readDatabaseWithExpandIdAndAsPropertyTest()
    {
        // Query '$expand=person/contacts/email'
        $queryString = vsprintf(
            '%s=person%scontacts%s%s%sperson-data',
            [
                Constants::EXPAND_KEYWORD,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
                Constants::DATA_ID_KEY,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
            ]
        );
        parse_str($queryString, $query);
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest('GET', '/loaned/', $query)
        );
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            HandlerResultInterface::class,
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());

        $this->assertInstanceOf(SplFixedArray::class, $handlerResult->getData());
        $this->assertEquals(4, $handlerResult->getData()->count());

        /** @var DocumentInterface $dataInstance */
        $dataInstance = $handlerResult->getData()->current();
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person'));
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person-data.email'));
        $handlerResult->getData()->next();
        $handlerResult->getData()->next();
        $dataInstance = $handlerResult->getData()->current();
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person'));
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person-data.email'));
    }

    /**
     * @test
     */
    public function readDatabaseWithMoreThanOneExpandsAndAsPropertyTest()
    {
        // Query '$expand=person/contacts/email/-/book/book/isbn_10'
        $queryString = vsprintf(
            '%s=person%scontacts%semail%sperson-data%sbook%sbook%sisbn_10',
            [
                Constants::EXPAND_KEYWORD,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
                Constants::EXPAND_REQUEST_DELIMITER,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
            ]
        );
        parse_str($queryString, $query);
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest('GET', '/loaned/', $query)
        );
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            HandlerResultInterface::class,
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());

        $this->assertInstanceOf(SplFixedArray::class, $handlerResult->getData());
        $this->assertEquals(4, $handlerResult->getData()->count());

        /** @var DocumentInterface $dataInstance */
        $dataInstance = $handlerResult->getData()->current();
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person'));
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person-data.email'));
        $this->assertEquals('0395595118', $dataInstance->valueForKeyPath('book.isbn_10'));
        $this->assertEquals('The Lord of the rings', $dataInstance->valueForKeyPath('book.title'));
        $handlerResult->getData()->next();
        $handlerResult->getData()->next();
        $dataInstance = $handlerResult->getData()->current();
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person'));
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person-data.email'));
        $this->assertEquals('0345253426', $dataInstance->valueForKeyPath('book.isbn_10'));
        $this->assertEquals('The Hobbit', $dataInstance->valueForKeyPath('book.title'));
    }

    /**
     * @test
     */
    public function readDocumentWithExpandAndAsPropertyTest()
    {
        // Query '$expand=person/contacts/email'
        $queryString = vsprintf(
            '%s=person%scontacts%semail%sperson-data',
            [
                Constants::EXPAND_KEYWORD,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
            ]
        );
        parse_str($queryString, $query);
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'GET',
                '/loaned/L1420194884',
                $query
            )
        );
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            HandlerResultInterface::class,
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());
        $this->assertInstanceOf(
            DocumentInterface::class,
            $handlerResult->getData()
        );

        /** @var DocumentInterface $dataInstance */
        $dataInstance = $handlerResult->getData();
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person'));
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person-data.email'));
    }

    /**
     * @test
     */
    public function readDocumentWithExpandIdAndAsPropertyTest()
    {
        // Query '$expand=person/contacts/email'
        $queryString = vsprintf(
            '%s=person%scontacts%s%s%sperson-data',
            [
                Constants::EXPAND_KEYWORD,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
                Constants::DATA_ID_KEY,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
            ]
        );
        parse_str($queryString, $query);
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'GET',
                '/loaned/L1420194884',
                $query
            )
        );
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            HandlerResultInterface::class,
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());
        $this->assertInstanceOf(
            DocumentInterface::class,
            $handlerResult->getData()
        );

        /** @var DocumentInterface $dataInstance */
        $dataInstance = $handlerResult->getData();
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person'));
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person-data.email'));
    }

    /**
     * @test
     */
    public function readDocumentWithMoreThanOneExpandsAndAsPropertyTest()
    {
        // Query '$expand=person/contacts/email/-/book/book/isbn_10'
        $queryString = vsprintf(
            '%s=person%scontacts%semail%sperson-data%sbook%sbook%sisbn_10',
            [
                Constants::EXPAND_KEYWORD,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
                Constants::EXPAND_REQUEST_DELIMITER,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
            ]
        );
        parse_str($queryString, $query);
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'GET',
                '/loaned/L1420194884',
                $query
            )
        );
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            HandlerResultInterface::class,
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());
        $this->assertInstanceOf(
            DocumentInterface::class,
            $handlerResult->getData()
        );

        /** @var DocumentInterface $dataInstance */
        $dataInstance = $handlerResult->getData();
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person'));
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person-data.email'));
        $this->assertEquals('0345253426', $dataInstance->valueForKeyPath('book.isbn_10'));
        $this->assertEquals('The Hobbit', $dataInstance->valueForKeyPath('book.title'));
    }

    /**
     * @test
     */
    public function readWithSearchAndExpandAndAsPropertyTest()
    {
        // Query 'title=The Hobbit&$expand=person/contacts/email'
        $queryString = vsprintf(
            'title=The Hobbit&%s=person%scontacts%semail%sperson-data',
            [
                Constants::EXPAND_KEYWORD,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
            ]
        );
        parse_str($queryString, $query);
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest('GET', '/loaned/', $query)
        );
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            HandlerResultInterface::class,
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());

        $this->assertInstanceOf(SplFixedArray::class, $handlerResult->getData());
        $this->assertEquals(1, $handlerResult->getData()->count());

        /** @var DocumentInterface $dataInstance */
        $dataInstance = $handlerResult->getData()->current();
        $this->assertEquals('The Hobbit', $dataInstance->valueForKeyPath('title'));
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person'));
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person-data.email'));


        // Query '$expand=person/contacts/email&title=The Hobbit'
        $queryString = vsprintf(
            '%s=person%scontacts%semail%sperson-data&title=The Hobbit',
            [
                Constants::EXPAND_KEYWORD,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
            ]
        );
        parse_str($queryString, $query);
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest('GET', '/loaned/', $query)
        );
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            HandlerResultInterface::class,
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());

        $this->assertInstanceOf(SplFixedArray::class, $handlerResult->getData());
        $this->assertEquals(1, $handlerResult->getData()->count());

        /** @var DocumentInterface $dataInstance */
        $dataInstance = $handlerResult->getData()->current();
        $this->assertEquals('The Hobbit', $dataInstance->valueForKeyPath('title'));
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person'));
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person-data.email'));
    }

    /**
     * @test
     */
    public function readWithEmptyResultSearchAndExpandAndAsPropertyTest()
    {
        // Query 'firstName=Some-thing-not-existing&$expand=person/contacts/email'
        $queryString = vsprintf(
            'firstName=Some-thing-not-existing&%s=person%scontacts%semail%sperson-data',
            [
                Constants::EXPAND_KEYWORD,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
            ]
        );
        parse_str($queryString, $query);
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest('GET', '/contacts/', $query)
        );
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            HandlerResultInterface::class,
            $handlerResult
        );
        $this->assertEquals(404, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());
        $this->assertInstanceOf(FilterResultInterface::class, $handlerResult->getData());
        $this->assertEquals(0, $handlerResult->getData()->count());


        // Query 'some-thing-not-existing=Daniel&$expand=person/contacts/email'
        $queryString = vsprintf(
            'some-thing-not-existing=Daniel&%s=person%scontacts%semail%sperson-data',
            [
                Constants::EXPAND_KEYWORD,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
                Constants::EXPAND_REQUEST_SPLIT_CHAR,
            ]
        );
        parse_str($queryString, $query);
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest('GET', '/contacts/', $query)
        );
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            HandlerResultInterface::class,
            $handlerResult
        );
        $this->assertEquals(404, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());
        $this->assertInstanceOf(
            FilterResultInterface::class,
            $handlerResult->getData()
        );
        $this->assertEquals(0, $handlerResult->getData()->count());
    }

    protected function setUp()
    {
        if (class_exists(Manager::class)) {
            Manager::freeAll();
        }

        $diContainer = $this->getDiContainer();
        $this->requestInfoFactory = $diContainer->get(RequestInfoFactory::class);

//        $server = $diContainer->get(DummyServer::class);
//        $diContainer->set(ServerInterface::class, $server);

        $coordinator = $diContainer->get(CoordinatorInterface::class);

        $this->setUpXhprof();

        /** @var ServerInterface $server */
        $server = $this->prophesize(ServerInterface::class)->reveal();
        /** @var SharedEventEmitter $eventEmitter */
        $eventEmitter = $this->prophesize(SharedEventEmitter::class)->reveal();
        /** @var FilterBuilderInterface $filterBuilder */
        $filterBuilder = $this->prophesize(FilterBuilderInterface::class)->reveal();
        /** @var ExpandConfigurationBuilderInterface $expandConfigurationBuilder */
        $expandConfigurationBuilder = $this->prophesize(ExpandConfigurationBuilderInterface::class)->reveal();
        /** @var ExpandResolverInterface $expandResolver */
        $expandResolver = $this->prophesize(ExpandResolverInterface::class)->reveal();
        /** @var AssetProviderInterface $assetLoader */
        $assetLoader = $this->prophesize(AssetProviderInterface::class)->reveal();

        $this->fixture = new Handler(
            $server,
            $eventEmitter,
            $coordinator,
            $filterBuilder,
            $expandConfigurationBuilder,
            $expandResolver,
            $assetLoader
        );
        $this->fixture = $this->getDiContainer()->get(Handler::class);
//        $this->database = $coordinator->getDatabase('contacts');
    }

    protected function tearDown()
    {
        if (class_exists(Manager::class)) {
            Manager::freeAll();
        }
    }
}
