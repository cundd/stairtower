<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 11.10.14
 * Time: 20:18
 */

namespace Cundd\PersistentObjectStore\Server\Handler;


use Cundd\PersistentObjectStore\AbstractCase;
use Cundd\PersistentObjectStore\Constants;
use Cundd\PersistentObjectStore\Domain\Model\DatabaseInterface;
use Cundd\PersistentObjectStore\Domain\Model\DocumentInterface;
use Cundd\PersistentObjectStore\Memory\Manager;
use Cundd\PersistentObjectStore\Server\ValueObject\RequestInfoFactory;
use React\Http\Request;

/**
 * Handler test
 *
 * @package Cundd\PersistentObjectStore\Server\Handler
 */
class HandlerExpandTest extends AbstractCase
{
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
        $queryString = vsprintf('%s=person%scontacts%semail', [
            Constants::EXPAND_KEYWORD,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
        ]);
        parse_str($queryString, $query);
        $requestInfo   = $this->requestInfoFactory->buildRequestFromRawRequest(new Request('GET', '/loaned/', $query));
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface',
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());

        $this->assertInstanceOf('SplFixedArray', $handlerResult->getData());
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
        $queryString = vsprintf('%s=person%scontacts%s%s', [
            Constants::EXPAND_KEYWORD,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
            Constants::DATA_ID_KEY,
        ]);
        parse_str($queryString, $query);
        $requestInfo   = $this->requestInfoFactory->buildRequestFromRawRequest(new Request('GET', '/loaned/', $query));
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface',
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());

        $this->assertInstanceOf('SplFixedArray', $handlerResult->getData());
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
        $queryString = vsprintf('%s=person%scontacts%semail%sbook%sbook%sisbn_10', [
            Constants::EXPAND_KEYWORD,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
            Constants::EXPAND_REQUEST_DELIMITER,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
        ]);
        parse_str($queryString, $query);
        $requestInfo   = $this->requestInfoFactory->buildRequestFromRawRequest(new Request('GET', '/loaned/', $query));
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface',
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());

        $this->assertInstanceOf('SplFixedArray', $handlerResult->getData());
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
        $queryString = vsprintf('%s=person%scontacts%semail', [
            Constants::EXPAND_KEYWORD,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
        ]);
        parse_str($queryString, $query);
        $requestInfo   = $this->requestInfoFactory->buildRequestFromRawRequest(new Request('GET', '/loaned/L1420194884',
            $query));
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface',
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Domain\\Model\\DocumentInterface',
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
        $queryString = vsprintf('%s=person%scontacts%s%s', [
            Constants::EXPAND_KEYWORD,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
            Constants::DATA_ID_KEY,
        ]);
        parse_str($queryString, $query);
        $requestInfo   = $this->requestInfoFactory->buildRequestFromRawRequest(new Request('GET', '/loaned/L1420194884',
            $query));
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface',
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Domain\\Model\\DocumentInterface',
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
        $queryString = vsprintf('%s=person%scontacts%semail%sbook%sbook%sisbn_10', [
            Constants::EXPAND_KEYWORD,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
            Constants::EXPAND_REQUEST_DELIMITER,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
        ]);
        parse_str($queryString, $query);
        $requestInfo   = $this->requestInfoFactory->buildRequestFromRawRequest(new Request('GET', '/loaned/L1420194884',
            $query));
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface',
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Domain\\Model\\DocumentInterface',
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
        $queryString = vsprintf('title=The Hobbit&%s=person%scontacts%semail', [
            Constants::EXPAND_KEYWORD,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
        ]);
        parse_str($queryString, $query);
        $requestInfo   = $this->requestInfoFactory->buildRequestFromRawRequest(new Request('GET', '/loaned/', $query));
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface',
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());

        $this->assertInstanceOf('SplFixedArray', $handlerResult->getData());
        $this->assertEquals(1, $handlerResult->getData()->count());

        /** @var DocumentInterface $dataInstance */
        $dataInstance = $handlerResult->getData()->current();
        $this->assertEquals('The Hobbit', $dataInstance->valueForKeyPath('title'));
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person.email'));


        // Query '$expand=person/contacts/email&title=The Hobbit'
        $queryString = vsprintf('%s=person%scontacts%semail&title=The Hobbit', [
            Constants::EXPAND_KEYWORD,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
        ]);
        parse_str($queryString, $query);
        $requestInfo   = $this->requestInfoFactory->buildRequestFromRawRequest(new Request('GET', '/loaned/', $query));
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface',
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());

        $this->assertInstanceOf('SplFixedArray', $handlerResult->getData());
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
        $queryString = vsprintf('firstName=Some-thing-not-existing&%s=person%scontacts%semail', [
            Constants::EXPAND_KEYWORD,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
        ]);
        parse_str($queryString, $query);
        $requestInfo   = $this->requestInfoFactory->buildRequestFromRawRequest(new Request('GET', '/contacts/', $query));
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface',
            $handlerResult
        );
        $this->assertEquals(404, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Filter\\FilterResultInterface',
            $handlerResult->getData()
        );
        $this->assertEquals(0, $handlerResult->getData()->count());


        // Query 'some-thing-not-existing=Daniel&$expand=person/contacts/email'
        $queryString = vsprintf('some-thing-not-existing=Daniel&%s=person%scontacts%semail', [
            Constants::EXPAND_KEYWORD,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
        ]);
        parse_str($queryString, $query);
        $requestInfo   = $this->requestInfoFactory->buildRequestFromRawRequest(new Request('GET', '/contacts/', $query));
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface',
            $handlerResult
        );
        $this->assertEquals(404, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Filter\\FilterResultInterface',
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
        $queryString = vsprintf('%s=person%scontacts%semail%sperson-data', [
            Constants::EXPAND_KEYWORD,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
        ]);
        parse_str($queryString, $query);
        $requestInfo   = $this->requestInfoFactory->buildRequestFromRawRequest(new Request('GET', '/loaned/', $query));
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface',
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());

        $this->assertInstanceOf('SplFixedArray', $handlerResult->getData());
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
        $queryString = vsprintf('%s=person%scontacts%s%s%sperson-data', [
            Constants::EXPAND_KEYWORD,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
            Constants::DATA_ID_KEY,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
        ]);
        parse_str($queryString, $query);
        $requestInfo   = $this->requestInfoFactory->buildRequestFromRawRequest(new Request('GET', '/loaned/', $query));
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface',
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());

        $this->assertInstanceOf('SplFixedArray', $handlerResult->getData());
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
        $queryString = vsprintf('%s=person%scontacts%semail%sperson-data%sbook%sbook%sisbn_10', [
            Constants::EXPAND_KEYWORD,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
            Constants::EXPAND_REQUEST_DELIMITER,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
        ]);
        parse_str($queryString, $query);
        $requestInfo   = $this->requestInfoFactory->buildRequestFromRawRequest(new Request('GET', '/loaned/', $query));
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface',
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());

        $this->assertInstanceOf('SplFixedArray', $handlerResult->getData());
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
        $queryString = vsprintf('%s=person%scontacts%semail%sperson-data', [
            Constants::EXPAND_KEYWORD,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
        ]);
        parse_str($queryString, $query);
        $requestInfo   = $this->requestInfoFactory->buildRequestFromRawRequest(new Request('GET', '/loaned/L1420194884',
            $query));
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface',
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Domain\\Model\\DocumentInterface',
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
        $queryString = vsprintf('%s=person%scontacts%s%s%sperson-data', [
            Constants::EXPAND_KEYWORD,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
            Constants::DATA_ID_KEY,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
        ]);
        parse_str($queryString, $query);
        $requestInfo   = $this->requestInfoFactory->buildRequestFromRawRequest(new Request('GET', '/loaned/L1420194884',
            $query));
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface',
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Domain\\Model\\DocumentInterface',
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
        $queryString = vsprintf('%s=person%scontacts%semail%sperson-data%sbook%sbook%sisbn_10', [
            Constants::EXPAND_KEYWORD,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
            Constants::EXPAND_REQUEST_DELIMITER,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
        ]);
        parse_str($queryString, $query);
        $requestInfo   = $this->requestInfoFactory->buildRequestFromRawRequest(new Request('GET', '/loaned/L1420194884',
            $query));
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface',
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Domain\\Model\\DocumentInterface',
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
        $queryString = vsprintf('title=The Hobbit&%s=person%scontacts%semail%sperson-data', [
            Constants::EXPAND_KEYWORD,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
        ]);
        parse_str($queryString, $query);
        $requestInfo   = $this->requestInfoFactory->buildRequestFromRawRequest(new Request('GET', '/loaned/', $query));
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface',
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());

        $this->assertInstanceOf('SplFixedArray', $handlerResult->getData());
        $this->assertEquals(1, $handlerResult->getData()->count());

        /** @var DocumentInterface $dataInstance */
        $dataInstance = $handlerResult->getData()->current();
        $this->assertEquals('The Hobbit', $dataInstance->valueForKeyPath('title'));
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person'));
        $this->assertEquals('info@cundd.net', $dataInstance->valueForKeyPath('person-data.email'));


        // Query '$expand=person/contacts/email&title=The Hobbit'
        $queryString = vsprintf('%s=person%scontacts%semail%sperson-data&title=The Hobbit', [
            Constants::EXPAND_KEYWORD,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
        ]);
        parse_str($queryString, $query);
        $requestInfo   = $this->requestInfoFactory->buildRequestFromRawRequest(new Request('GET', '/loaned/', $query));
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface',
            $handlerResult
        );
        $this->assertEquals(200, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());

        $this->assertInstanceOf('SplFixedArray', $handlerResult->getData());
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
        $queryString = vsprintf('firstName=Some-thing-not-existing&%s=person%scontacts%semail%sperson-data', [
            Constants::EXPAND_KEYWORD,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
        ]);
        parse_str($queryString, $query);
        $requestInfo   = $this->requestInfoFactory->buildRequestFromRawRequest(new Request('GET', '/contacts/', $query));
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface',
            $handlerResult
        );
        $this->assertEquals(404, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Filter\\FilterResultInterface',
            $handlerResult->getData()
        );
        $this->assertEquals(0, $handlerResult->getData()->count());


        // Query 'some-thing-not-existing=Daniel&$expand=person/contacts/email'
        $queryString = vsprintf('some-thing-not-existing=Daniel&%s=person%scontacts%semail%sperson-data', [
            Constants::EXPAND_KEYWORD,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
            Constants::EXPAND_REQUEST_SPLIT_CHAR,
        ]);
        parse_str($queryString, $query);
        $requestInfo   = $this->requestInfoFactory->buildRequestFromRawRequest(new Request('GET', '/contacts/', $query));
        $handlerResult = $this->fixture->read($requestInfo);
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerResultInterface',
            $handlerResult
        );
        $this->assertEquals(404, $handlerResult->getStatusCode());
        $this->assertNotNull($handlerResult->getData());
        $this->assertInstanceOf(
            'Cundd\\PersistentObjectStore\\Filter\\FilterResultInterface',
            $handlerResult->getData()
        );
        $this->assertEquals(0, $handlerResult->getData()->count());
    }

    protected function setUp()
    {
        if (class_exists('Cundd\PersistentObjectStore\Memory\Manager')) {
            Manager::freeAll();
        }

        $diContainer = $this->getDiContainer();

        $this->requestInfoFactory = $diContainer->get('Cundd\\PersistentObjectStore\\Server\\ValueObject\\RequestInfoFactory');
            
        $server      = $diContainer->get('Cundd\\PersistentObjectStore\\Server\\DummyServer');
        $diContainer->set('Cundd\\PersistentObjectStore\\Server\\ServerInterface', $server);

        $coordinator = $diContainer->get('Cundd\\PersistentObjectStore\\DataAccess\\CoordinatorInterface');

        $this->setUpXhprof();

        $this->fixture  = $this->getDiContainer()->get('Cundd\\PersistentObjectStore\\Server\\Handler\\Handler');
        $this->database = $coordinator->getDatabase('contacts');
    }

    protected function tearDown()
    {
        Manager::freeAll();
    }
}
