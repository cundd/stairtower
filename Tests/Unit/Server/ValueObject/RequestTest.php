<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Tests\Unit\Server\ValueObject;

use Cundd\Stairtower\Server\Handler\Handler;
use Cundd\Stairtower\Server\Handler\HandlerInterface;
use Cundd\Stairtower\Server\ValueObject\RequestInfoFactory;
use Cundd\Stairtower\Tests\Fixtures\TestApplication;
use Cundd\Stairtower\Tests\Fixtures\TestApplicationController;
use Cundd\Stairtower\Tests\Unit\AbstractCase;
use Cundd\Stairtower\Tests\Unit\RequestBuilderTrait;


/**
 * Tests for creating Request objects
 */
class RequestTest extends AbstractCase
{
    use RequestBuilderTrait;

    /**
     * @var RequestInfoFactory
     */
    protected $requestInfoFactory;


    protected function setUp()
    {
        $this->requestInfoFactory = $this->getDiContainer()->get(RequestInfoFactory::class);
        $this->makeClassAliasIfNotExists(
            TestApplication::class,
            'Cundd\\Special\\Application'
        );
        $this->makeClassAliasIfNotExists(
            TestApplicationController::class,
            'Cundd\\Test\\Controller\\ApplicationController'
        );
        $this->makeClassAliasIfNotExists(
            TestApplicationController::class,
            'Cundd\\TestModule\\Controller\\ApplicationController'
        );
    }


    /**
     * @test
     */
    public function buildRequestInfoFromRequestTests()
    {
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'GET',
                '/contacts/info@cundd.net'
            )
        );
        $this->assertEquals('GET', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('info@cundd.net', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'HEAD',
                '/contacts/info@cundd.net'
            )
        );
        $this->assertEquals('HEAD', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('info@cundd.net', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'POST',
                '/contacts/info@cundd.net'
            )
        );
        $this->assertEquals('POST', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('info@cundd.net', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'PUT',
                '/contacts/info@cundd.net'
            )
        );
        $this->assertEquals('PUT', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('info@cundd.net', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'DELETE',
                '/contacts/info@cundd.net'
            )
        );
        $this->assertEquals('DELETE', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('info@cundd.net', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());


        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'GET',
                '/contacts/'
            )
        );
        $this->assertEquals('GET', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertSame('', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'HEAD',
                '/contacts/'
            )
        );
        $this->assertEquals('HEAD', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertSame('', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'POST',
                '/contacts/'
            )
        );
        $this->assertEquals('POST', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertSame('', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'PUT',
                '/contacts/'
            )
        );
        $this->assertEquals('PUT', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertSame('', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'DELETE',
                '/contacts/'
            )
        );
        $this->assertEquals('DELETE', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertSame('', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());


        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'GET',
                '/contacts'
            )
        );
        $this->assertEquals('GET', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertSame('', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'HEAD',
                '/contacts'
            )
        );
        $this->assertEquals('HEAD', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertSame('', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'POST',
                '/contacts'
            )
        );
        $this->assertEquals('POST', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertSame('', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'PUT',
                '/contacts'
            )
        );
        $this->assertEquals('PUT', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertSame('', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'DELETE',
                '/contacts'
            )
        );
        $this->assertEquals('DELETE', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertSame('', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());


        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'GET',
                '/contacts/my-info-path'
            )
        );
        $this->assertEquals('GET', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'HEAD',
                '/contacts/my-info-path'
            )
        );
        $this->assertEquals('HEAD', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'POST',
                '/contacts/my-info-path'
            )
        );
        $this->assertEquals('POST', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'PUT',
                '/contacts/my-info-path'
            )
        );
        $this->assertEquals('PUT', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'DELETE',
                '/contacts/my-info-path'
            )
        );
        $this->assertEquals('DELETE', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());


        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'GET',
                '/contacts-database/my-info-path'
            )
        );
        $this->assertEquals('GET', $requestInfo->getMethod());
        $this->assertEquals('contacts-database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'HEAD',
                '/contacts-database/my-info-path'
            )
        );
        $this->assertEquals('HEAD', $requestInfo->getMethod());
        $this->assertEquals('contacts-database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'POST',
                '/contacts-database/my-info-path'
            )
        );
        $this->assertEquals('POST', $requestInfo->getMethod());
        $this->assertEquals('contacts-database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'PUT',
                '/contacts-database/my-info-path'
            )
        );
        $this->assertEquals('PUT', $requestInfo->getMethod());
        $this->assertEquals('contacts-database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'DELETE',
                '/contacts-database/my-info-path'
            )
        );
        $this->assertEquals('DELETE', $requestInfo->getMethod());
        $this->assertEquals('contacts-database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());


        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'GET',
                '/contacts_database/my_info-path'
            )
        );
        $this->assertEquals('GET', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my_info-path', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'HEAD',
                '/contacts_database/my_info-path'
            )
        );
        $this->assertEquals('HEAD', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my_info-path', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'POST',
                '/contacts_database/my_info-path'
            )
        );
        $this->assertEquals('POST', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my_info-path', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'PUT',
                '/contacts_database/my_info-path'
            )
        );
        $this->assertEquals('PUT', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my_info-path', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'DELETE',
                '/contacts_database/my_info-path'
            )
        );
        $this->assertEquals('DELETE', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my_info-path', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());


        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'GET',
                '/contacts_database/my-super_email@a-smthng.com'
            )
        );
        $this->assertEquals('GET', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'HEAD',
                '/contacts_database/my-super_email@a-smthng.com'
            )
        );
        $this->assertEquals('HEAD', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'POST',
                '/contacts_database/my-super_email@a-smthng.com'
            )
        );
        $this->assertEquals('POST', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'PUT',
                '/contacts_database/my-super_email@a-smthng.com'
            )
        );
        $this->assertEquals('PUT', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'DELETE',
                '/contacts_database/my-super_email@a-smthng.com'
            )
        );
        $this->assertEquals('DELETE', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());


        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'GET',
                '/contacts_database/my-super_email@a-smthng.com/something-more'
            )
        );
        $this->assertEquals('GET', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'HEAD',
                '/contacts_database/my-super_email@a-smthng.com/something-more'
            )
        );
        $this->assertEquals('HEAD', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'POST',
                '/contacts_database/my-super_email@a-smthng.com/something-more'
            )
        );
        $this->assertEquals('POST', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'PUT',
                '/contacts_database/my-super_email@a-smthng.com/something-more'
            )
        );
        $this->assertEquals('PUT', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'DELETE',
                '/contacts_database/my-super_email@a-smthng.com/something-more'
            )
        );
        $this->assertEquals('DELETE', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());
    }

    /**
     * @test
     */
    public function buildRequestInfoFromRequestWithControllerTest()
    {
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'GET',
                '/_cundd-test-application/my_method'
            )
        );
        $this->assertEquals('GET', $requestInfo->getMethod());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertEquals('Cundd\\Test\\Controller\\ApplicationController', $requestInfo->getControllerClass());
        $this->assertEquals('getMyMethodAction', $requestInfo->getAction());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('', $requestInfo->getDataIdentifier());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'HEAD',
                '/_cundd-test-application/my_method'
            )
        );
        $this->assertEquals('HEAD', $requestInfo->getMethod());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertEquals('Cundd\\Test\\Controller\\ApplicationController', $requestInfo->getControllerClass());
        $this->assertEquals('headMyMethodAction', $requestInfo->getAction());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('', $requestInfo->getDataIdentifier());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'POST',
                '/_cundd-test-application/my_method'
            )
        );
        $this->assertEquals('POST', $requestInfo->getMethod());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertEquals('Cundd\\Test\\Controller\\ApplicationController', $requestInfo->getControllerClass());
        $this->assertEquals('postMyMethodAction', $requestInfo->getAction());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('', $requestInfo->getDataIdentifier());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'PUT',
                '/_cundd-test-application/my_method'
            )
        );
        $this->assertEquals('PUT', $requestInfo->getMethod());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertEquals('Cundd\\Test\\Controller\\ApplicationController', $requestInfo->getControllerClass());
        $this->assertEquals('putMyMethodAction', $requestInfo->getAction());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('', $requestInfo->getDataIdentifier());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'DELETE',
                '/_cundd-test-application/my_method'
            )
        );
        $this->assertEquals('DELETE', $requestInfo->getMethod());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertEquals('Cundd\\Test\\Controller\\ApplicationController', $requestInfo->getControllerClass());
        $this->assertEquals('deleteMyMethodAction', $requestInfo->getAction());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('', $requestInfo->getDataIdentifier());


        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'GET',
                '/_cundd-notexistingtest-application/my_undefined_method'
            )
        );
        $this->assertEquals('GET', $requestInfo->getMethod());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my_undefined_method', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'HEAD',
                '/_cundd-notexistingtest-application/my_undefined_method'
            )
        );
        $this->assertEquals('HEAD', $requestInfo->getMethod());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my_undefined_method', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'POST',
                '/_cundd-notexistingtest-application/my_undefined_method'
            )
        );
        $this->assertEquals('POST', $requestInfo->getMethod());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my_undefined_method', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'PUT',
                '/_cundd-notexistingtest-application/my_undefined_method'
            )
        );
        $this->assertEquals('PUT', $requestInfo->getMethod());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my_undefined_method', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'DELETE',
                '/_cundd-notexistingtest-application/my_undefined_method'
            )
        );
        $this->assertEquals('DELETE', $requestInfo->getMethod());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my_undefined_method', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertSame('', $requestInfo->getAction());
        $this->assertSame('', $requestInfo->getControllerClass());


        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'GET',
                '/_cundd-test-application/my_undefined_method'
            )
        );
        $this->assertEquals('GET', $requestInfo->getMethod());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertEquals('Cundd\\Test\\Controller\\ApplicationController', $requestInfo->getControllerClass());
        $this->assertEquals('getMyUndefinedMethodAction', $requestInfo->getAction());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('', $requestInfo->getDataIdentifier());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'HEAD',
                '/_cundd-test-application/my_undefined_method'
            )
        );
        $this->assertEquals('HEAD', $requestInfo->getMethod());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertEquals('Cundd\\Test\\Controller\\ApplicationController', $requestInfo->getControllerClass());
        $this->assertEquals('headMyUndefinedMethodAction', $requestInfo->getAction());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('', $requestInfo->getDataIdentifier());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'POST',
                '/_cundd-test-application/my_undefined_method'
            )
        );
        $this->assertEquals('POST', $requestInfo->getMethod());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertEquals('Cundd\\Test\\Controller\\ApplicationController', $requestInfo->getControllerClass());
        $this->assertEquals('postMyUndefinedMethodAction', $requestInfo->getAction());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('', $requestInfo->getDataIdentifier());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'PUT',
                '/_cundd-test-application/my_undefined_method'
            )
        );
        $this->assertEquals('PUT', $requestInfo->getMethod());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertEquals('Cundd\\Test\\Controller\\ApplicationController', $requestInfo->getControllerClass());
        $this->assertEquals('putMyUndefinedMethodAction', $requestInfo->getAction());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('', $requestInfo->getDataIdentifier());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'DELETE',
                '/_cundd-test-application/my_undefined_method'
            )
        );
        $this->assertEquals('DELETE', $requestInfo->getMethod());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertEquals('Cundd\\Test\\Controller\\ApplicationController', $requestInfo->getControllerClass());
        $this->assertEquals('deleteMyUndefinedMethodAction', $requestInfo->getAction());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('', $requestInfo->getDataIdentifier());


        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'GET',
                '/_cundd-test_module-application/my_undefined_method'
            )
        );
        $this->assertEquals('GET', $requestInfo->getMethod());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertEquals('getMyUndefinedMethodAction', $requestInfo->getAction());
        $this->assertEquals('Cundd\\TestModule\\Controller\\ApplicationController', $requestInfo->getControllerClass());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('', $requestInfo->getDataIdentifier());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'HEAD',
                '/_cundd-test_module-application/my_undefined_method'
            )
        );
        $this->assertEquals('HEAD', $requestInfo->getMethod());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertEquals('headMyUndefinedMethodAction', $requestInfo->getAction());
        $this->assertEquals('Cundd\\TestModule\\Controller\\ApplicationController', $requestInfo->getControllerClass());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('', $requestInfo->getDataIdentifier());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'POST',
                '/_cundd-test_module-application/my_undefined_method'
            )
        );
        $this->assertEquals('POST', $requestInfo->getMethod());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertEquals('postMyUndefinedMethodAction', $requestInfo->getAction());
        $this->assertEquals('Cundd\\TestModule\\Controller\\ApplicationController', $requestInfo->getControllerClass());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('', $requestInfo->getDataIdentifier());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'PUT',
                '/_cundd-test_module-application/my_undefined_method'
            )
        );
        $this->assertEquals('PUT', $requestInfo->getMethod());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertEquals('putMyUndefinedMethodAction', $requestInfo->getAction());
        $this->assertEquals('Cundd\\TestModule\\Controller\\ApplicationController', $requestInfo->getControllerClass());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('', $requestInfo->getDataIdentifier());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'DELETE',
                '/_cundd-test_module-application/my_undefined_method'
            )
        );
        $this->assertEquals('DELETE', $requestInfo->getMethod());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertEquals('deleteMyUndefinedMethodAction', $requestInfo->getAction());
        $this->assertEquals('Cundd\\TestModule\\Controller\\ApplicationController', $requestInfo->getControllerClass());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('', $requestInfo->getDataIdentifier());


        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'GET',
                '/_cundd-testModule-application/my_undefined_method'
            )
        );
        $this->assertEquals('GET', $requestInfo->getMethod());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertEquals('getMyUndefinedMethodAction', $requestInfo->getAction());
        $this->assertEquals('Cundd\\TestModule\\Controller\\ApplicationController', $requestInfo->getControllerClass());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('', $requestInfo->getDataIdentifier());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'HEAD',
                '/_cundd-testModule-application/my_undefined_method'
            )
        );
        $this->assertEquals('HEAD', $requestInfo->getMethod());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertEquals('headMyUndefinedMethodAction', $requestInfo->getAction());
        $this->assertEquals('Cundd\\TestModule\\Controller\\ApplicationController', $requestInfo->getControllerClass());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('', $requestInfo->getDataIdentifier());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'POST',
                '/_cundd-testModule-application/my_undefined_method'
            )
        );
        $this->assertEquals('POST', $requestInfo->getMethod());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertEquals('postMyUndefinedMethodAction', $requestInfo->getAction());
        $this->assertEquals('Cundd\\TestModule\\Controller\\ApplicationController', $requestInfo->getControllerClass());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('', $requestInfo->getDataIdentifier());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'PUT',
                '/_cundd-testModule-application/my_undefined_method'
            )
        );
        $this->assertEquals('PUT', $requestInfo->getMethod());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertEquals('putMyUndefinedMethodAction', $requestInfo->getAction());
        $this->assertEquals('Cundd\\TestModule\\Controller\\ApplicationController', $requestInfo->getControllerClass());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('', $requestInfo->getDataIdentifier());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'DELETE',
                '/_cundd-testModule-application/my_undefined_method'
            )
        );
        $this->assertEquals('DELETE', $requestInfo->getMethod());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertEquals('deleteMyUndefinedMethodAction', $requestInfo->getAction());
        $this->assertEquals('Cundd\\TestModule\\Controller\\ApplicationController', $requestInfo->getControllerClass());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('', $requestInfo->getDataIdentifier());


        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'GET',
                '/_cundd-test-application/my_method/my-database/data-identifier'
            )
        );
        $this->assertEquals('GET', $requestInfo->getMethod());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertEquals('Cundd\\Test\\Controller\\ApplicationController', $requestInfo->getControllerClass());
        $this->assertEquals('getMyMethodAction', $requestInfo->getAction());
        $this->assertEquals('my-database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('data-identifier', $requestInfo->getDataIdentifier());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'HEAD',
                '/_cundd-test-application/my_method/my-database/data-identifier'
            )
        );
        $this->assertEquals('HEAD', $requestInfo->getMethod());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertEquals('Cundd\\Test\\Controller\\ApplicationController', $requestInfo->getControllerClass());
        $this->assertEquals('headMyMethodAction', $requestInfo->getAction());
        $this->assertEquals('my-database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('data-identifier', $requestInfo->getDataIdentifier());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'POST',
                '/_cundd-test-application/my_method/my-database/data-identifier'
            )
        );
        $this->assertEquals('POST', $requestInfo->getMethod());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertEquals('Cundd\\Test\\Controller\\ApplicationController', $requestInfo->getControllerClass());
        $this->assertEquals('postMyMethodAction', $requestInfo->getAction());
        $this->assertEquals('my-database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('data-identifier', $requestInfo->getDataIdentifier());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'PUT',
                '/_cundd-test-application/my_method/my-database/data-identifier'
            )
        );
        $this->assertEquals('PUT', $requestInfo->getMethod());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertEquals('Cundd\\Test\\Controller\\ApplicationController', $requestInfo->getControllerClass());
        $this->assertEquals('putMyMethodAction', $requestInfo->getAction());
        $this->assertEquals('my-database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('data-identifier', $requestInfo->getDataIdentifier());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'DELETE',
                '/_cundd-test-application/my_method/my-database/data-identifier'
            )
        );
        $this->assertEquals('DELETE', $requestInfo->getMethod());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertEquals('Cundd\\Test\\Controller\\ApplicationController', $requestInfo->getControllerClass());
        $this->assertEquals('deleteMyMethodAction', $requestInfo->getAction());
        $this->assertEquals('my-database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('data-identifier', $requestInfo->getDataIdentifier());


        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'GET',
                '/_cundd-test-application/my_undefined_method/my-database/data-identifier'
            )
        );
        $this->assertEquals('GET', $requestInfo->getMethod());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertEquals('Cundd\\Test\\Controller\\ApplicationController', $requestInfo->getControllerClass());
        $this->assertEquals('getMyUndefinedMethodAction', $requestInfo->getAction());
        $this->assertEquals('my-database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('data-identifier', $requestInfo->getDataIdentifier());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'HEAD',
                '/_cundd-test-application/my_undefined_method/my-database/data-identifier'
            )
        );
        $this->assertEquals('HEAD', $requestInfo->getMethod());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertEquals('Cundd\\Test\\Controller\\ApplicationController', $requestInfo->getControllerClass());
        $this->assertEquals('headMyUndefinedMethodAction', $requestInfo->getAction());
        $this->assertEquals('my-database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('data-identifier', $requestInfo->getDataIdentifier());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'POST',
                '/_cundd-test-application/my_undefined_method/my-database/data-identifier'
            )
        );
        $this->assertEquals('POST', $requestInfo->getMethod());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertEquals('Cundd\\Test\\Controller\\ApplicationController', $requestInfo->getControllerClass());
        $this->assertEquals('postMyUndefinedMethodAction', $requestInfo->getAction());
        $this->assertEquals('my-database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('data-identifier', $requestInfo->getDataIdentifier());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'PUT',
                '/_cundd-test-application/my_undefined_method/my-database/data-identifier'
            )
        );
        $this->assertEquals('PUT', $requestInfo->getMethod());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertEquals('Cundd\\Test\\Controller\\ApplicationController', $requestInfo->getControllerClass());
        $this->assertEquals('putMyUndefinedMethodAction', $requestInfo->getAction());
        $this->assertEquals('my-database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('data-identifier', $requestInfo->getDataIdentifier());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'DELETE',
                '/_cundd-test-application/my_undefined_method/my-database/data-identifier'
            )
        );
        $this->assertEquals('DELETE', $requestInfo->getMethod());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertEquals('Cundd\\Test\\Controller\\ApplicationController', $requestInfo->getControllerClass());
        $this->assertEquals('deleteMyUndefinedMethodAction', $requestInfo->getAction());
        $this->assertEquals('my-database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('data-identifier', $requestInfo->getDataIdentifier());


        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'GET',
                '/_cundd-test_module-application/my_undefined_method/my-database'
            )
        );
        $this->assertEquals('GET', $requestInfo->getMethod());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertEquals('getMyUndefinedMethodAction', $requestInfo->getAction());
        $this->assertEquals('Cundd\\TestModule\\Controller\\ApplicationController', $requestInfo->getControllerClass());
        $this->assertEquals('my-database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('', $requestInfo->getDataIdentifier());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'HEAD',
                '/_cundd-test_module-application/my_undefined_method/my-database'
            )
        );
        $this->assertEquals('HEAD', $requestInfo->getMethod());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertEquals('headMyUndefinedMethodAction', $requestInfo->getAction());
        $this->assertEquals('Cundd\\TestModule\\Controller\\ApplicationController', $requestInfo->getControllerClass());
        $this->assertEquals('my-database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('', $requestInfo->getDataIdentifier());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'POST',
                '/_cundd-test_module-application/my_undefined_method/my-database'
            )
        );
        $this->assertEquals('POST', $requestInfo->getMethod());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertEquals('postMyUndefinedMethodAction', $requestInfo->getAction());
        $this->assertEquals('Cundd\\TestModule\\Controller\\ApplicationController', $requestInfo->getControllerClass());
        $this->assertEquals('my-database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('', $requestInfo->getDataIdentifier());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'PUT',
                '/_cundd-test_module-application/my_undefined_method/my-database'
            )
        );
        $this->assertEquals('PUT', $requestInfo->getMethod());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertEquals('putMyUndefinedMethodAction', $requestInfo->getAction());
        $this->assertEquals('Cundd\\TestModule\\Controller\\ApplicationController', $requestInfo->getControllerClass());
        $this->assertEquals('my-database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('', $requestInfo->getDataIdentifier());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'DELETE',
                '/_cundd-test_module-application/my_undefined_method/my-database'
            )
        );
        $this->assertEquals('DELETE', $requestInfo->getMethod());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertEquals('deleteMyUndefinedMethodAction', $requestInfo->getAction());
        $this->assertEquals('Cundd\\TestModule\\Controller\\ApplicationController', $requestInfo->getControllerClass());
        $this->assertEquals('my-database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('', $requestInfo->getDataIdentifier());


        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'GET',
                '/_cundd-testModule-application/my_undefined_method/my-database/data-identifier'
            )
        );
        $this->assertEquals('GET', $requestInfo->getMethod());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertEquals('getMyUndefinedMethodAction', $requestInfo->getAction());
        $this->assertEquals('Cundd\\TestModule\\Controller\\ApplicationController', $requestInfo->getControllerClass());
        $this->assertEquals('my-database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('data-identifier', $requestInfo->getDataIdentifier());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'HEAD',
                '/_cundd-testModule-application/my_undefined_method/my-database/data-identifier'
            )
        );
        $this->assertEquals('HEAD', $requestInfo->getMethod());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertEquals('headMyUndefinedMethodAction', $requestInfo->getAction());
        $this->assertEquals('Cundd\\TestModule\\Controller\\ApplicationController', $requestInfo->getControllerClass());
        $this->assertEquals('my-database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('data-identifier', $requestInfo->getDataIdentifier());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'POST',
                '/_cundd-testModule-application/my_undefined_method/my-database/data-identifier'
            )
        );
        $this->assertEquals('POST', $requestInfo->getMethod());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertEquals('postMyUndefinedMethodAction', $requestInfo->getAction());
        $this->assertEquals('Cundd\\TestModule\\Controller\\ApplicationController', $requestInfo->getControllerClass());
        $this->assertEquals('my-database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('data-identifier', $requestInfo->getDataIdentifier());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'PUT',
                '/_cundd-testModule-application/my_undefined_method/my-database/data-identifier'
            )
        );
        $this->assertEquals('PUT', $requestInfo->getMethod());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertEquals('putMyUndefinedMethodAction', $requestInfo->getAction());
        $this->assertEquals('Cundd\\TestModule\\Controller\\ApplicationController', $requestInfo->getControllerClass());
        $this->assertEquals('my-database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('data-identifier', $requestInfo->getDataIdentifier());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            $this->buildRequest(
                'DELETE',
                '/_cundd-testModule-application/my_undefined_method/my-database/data-identifier'
            )
        );
        $this->assertEquals('DELETE', $requestInfo->getMethod());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertEquals('deleteMyUndefinedMethodAction', $requestInfo->getAction());
        $this->assertEquals('Cundd\\TestModule\\Controller\\ApplicationController', $requestInfo->getControllerClass());
        $this->assertEquals('my-database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('data-identifier', $requestInfo->getDataIdentifier());
    }

    /**
     * @test
     */
    public function getServerActionForRequestTest()
    {
        $this->assertSame(
            '',
            RequestInfoFactory::getServerActionForRequest(
                $this->buildRequest('GET', '/_restart')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getServerActionForRequest(
                $this->buildRequest('GET', '/_restart/')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getServerActionForRequest(
                $this->buildRequest(
                    'GET',
                    '/_restart/something'
                )
            )
        );

        $this->assertSame(
            '',
            RequestInfoFactory::getServerActionForRequest(
                $this->buildRequest('HEAD', '/_restart')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getServerActionForRequest(
                $this->buildRequest('HEAD', '/_restart/')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getServerActionForRequest(
                $this->buildRequest(
                    'HEAD',
                    '/_restart/something'
                )
            )
        );

        $this->assertSame(
            '',
            RequestInfoFactory::getServerActionForRequest(
                $this->buildRequest('PUT', '/_restart')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getServerActionForRequest(
                $this->buildRequest('PUT', '/_restart/')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getServerActionForRequest(
                $this->buildRequest(
                    'PUT',
                    '/_restart/something'
                )
            )
        );

        $this->assertSame(
            '',
            RequestInfoFactory::getServerActionForRequest(
                $this->buildRequest('DELETE', '/_restart')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getServerActionForRequest(
                $this->buildRequest('DELETE', '/_restart/')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getServerActionForRequest(
                $this->buildRequest(
                    'DELETE',
                    '/_restart/something'
                )
            )
        );

        $this->assertEquals(
            'restart',
            RequestInfoFactory::getServerActionForRequest($this->buildRequest('POST', '/_restart'))
        );
        $this->assertEquals(
            'restart',
            RequestInfoFactory::getServerActionForRequest($this->buildRequest('POST', '/_restart/'))
        );
        $this->assertEquals(
            'restart',
            RequestInfoFactory::getServerActionForRequest(
                $this->buildRequest('POST', '/_restart/something')
            )
        );


        $this->assertSame(
            '',
            RequestInfoFactory::getServerActionForRequest(
                $this->buildRequest('GET', '/_shutdown')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getServerActionForRequest(
                $this->buildRequest('GET', '/_shutdown/')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getServerActionForRequest(
                $this->buildRequest(
                    'GET',
                    '/_shutdown/something'
                )
            )
        );

        $this->assertSame(
            '',
            RequestInfoFactory::getServerActionForRequest(
                $this->buildRequest('HEAD', '/_shutdown')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getServerActionForRequest(
                $this->buildRequest('HEAD', '/_shutdown/')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getServerActionForRequest(
                $this->buildRequest(
                    'HEAD',
                    '/_shutdown/something'
                )
            )
        );

        $this->assertSame(
            '',
            RequestInfoFactory::getServerActionForRequest(
                $this->buildRequest('PUT', '/_shutdown')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getServerActionForRequest(
                $this->buildRequest('PUT', '/_shutdown/')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getServerActionForRequest(
                $this->buildRequest(
                    'PUT',
                    '/_shutdown/something'
                )
            )
        );

        $this->assertSame(
            '',
            RequestInfoFactory::getServerActionForRequest(
                $this->buildRequest('DELETE', '/_shutdown')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getServerActionForRequest(
                $this->buildRequest('DELETE', '/_shutdown/')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getServerActionForRequest(
                $this->buildRequest(
                    'DELETE',
                    '/_shutdown/something'
                )
            )
        );

        $this->assertEquals(
            'shutdown',
            RequestInfoFactory::getServerActionForRequest($this->buildRequest('POST', '/_shutdown'))
        );
        $this->assertEquals(
            'shutdown',
            RequestInfoFactory::getServerActionForRequest($this->buildRequest('POST', '/_shutdown/'))
        );
        $this->assertEquals(
            'shutdown',
            RequestInfoFactory::getServerActionForRequest(
                $this->buildRequest('POST', '/_shutdown/something')
            )
        );


//		$this->assertSame('',RequestInfoFactory::getServerActionForRequest(\Cundd\Stairtower\Tests\Unit\$this->buildRequest('GET', '/_stop')));
//		$this->assertSame('',RequestInfoFactory::getServerActionForRequest(\Cundd\Stairtower\Tests\Unit\$this->buildRequest('GET', '/_stop/')));
//		$this->assertSame('',RequestInfoFactory::getServerActionForRequest(\Cundd\Stairtower\Tests\Unit\$this->buildRequest('GET', '/_stop/something')));
//
//		$this->assertSame('',RequestInfoFactory::getServerActionForRequest(\Cundd\Stairtower\Tests\Unit\$this->buildRequest('HEAD', '/_stop')));
//		$this->assertSame('',RequestInfoFactory::getServerActionForRequest(\Cundd\Stairtower\Tests\Unit\$this->buildRequest('HEAD', '/_stop/')));
//		$this->assertSame('',RequestInfoFactory::getServerActionForRequest(\Cundd\Stairtower\Tests\Unit\$this->buildRequest('HEAD', '/_stop/something')));
//
//		$this->assertSame('',RequestInfoFactory::getServerActionForRequest(\Cundd\Stairtower\Tests\Unit\$this->buildRequest('PUT', '/_stop')));
//		$this->assertSame('',RequestInfoFactory::getServerActionForRequest(\Cundd\Stairtower\Tests\Unit\$this->buildRequest('PUT', '/_stop/')));
//		$this->assertSame('',RequestInfoFactory::getServerActionForRequest(\Cundd\Stairtower\Tests\Unit\$this->buildRequest('PUT', '/_stop/something')));
//
//		$this->assertSame('',RequestInfoFactory::getServerActionForRequest(\Cundd\Stairtower\Tests\Unit\$this->buildRequest('DELETE', '/_stop')));
//		$this->assertSame('',RequestInfoFactory::getServerActionForRequest(\Cundd\Stairtower\Tests\Unit\$this->buildRequest('DELETE', '/_stop/')));
//		$this->assertSame('',RequestInfoFactory::getServerActionForRequest(\Cundd\Stairtower\Tests\Unit\$this->buildRequest('DELETE', '/_stop/something')));
//
//		$this->assertEquals('stop', RequestInfoFactory::getServerActionForRequest(\Cundd\Stairtower\Tests\Unit\$this->buildRequest('POST', '/_stop')));
//		$this->assertEquals('stop', RequestInfoFactory::getServerActionForRequest(\Cundd\Stairtower\Tests\Unit\$this->buildRequest('POST', '/_stop/')));
//		$this->assertEquals('stop', RequestInfoFactory::getServerActionForRequest(\Cundd\Stairtower\Tests\Unit\$this->buildRequest('POST', '/_stop/something')));

    }

    /**
     * @test
     */
    public function getHandlerActionForRequestTest()
    {
        $this->assertEquals(
            'getStatsAction',
            RequestInfoFactory::getHandlerActionForRequest($this->buildRequest('GET', '/_stats'))
        );
        $this->assertEquals(
            'getStatsAction',
            RequestInfoFactory::getHandlerActionForRequest($this->buildRequest('GET', '/_stats/'))
        );
        $this->assertEquals(
            'getStatsAction',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('GET', '/_stats/something')
            )
        );

        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('POST', '/_stats')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('POST', '/_stats/')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest(
                    'POST',
                    '/_stats/something'
                )
            )
        );

        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('PUT', '/_stats')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('PUT', '/_stats/')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('PUT', '/_stats/something')
            )
        );

        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('DELETE', '/_stats')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('DELETE', '/_stats/')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest(
                    'DELETE',
                    '/_stats/something'
                )
            )
        );

        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('HEAD', '/_stats')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('HEAD', '/_stats/')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest(
                    'HEAD',
                    '/_stats/something'
                )
            )
        );


        $this->assertEquals(
            'getAllDbsAction',
            RequestInfoFactory::getHandlerActionForRequest($this->buildRequest('GET', '/_all_dbs'))
        );
        $this->assertEquals(
            'getAllDbsAction',
            RequestInfoFactory::getHandlerActionForRequest($this->buildRequest('GET', '/_all_dbs/'))
        );

        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('POST', '/_all_dbs')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('POST', '/_all_dbs/')
            )
        );

        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('PUT', '/_all_dbs')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('PUT', '/_all_dbs/')
            )
        );

        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('DELETE', '/_all_dbs')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('DELETE', '/_all_dbs/')
            )
        );

        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('HEAD', '/_all_dbs')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('HEAD', '/_all_dbs/')
            )
        );


//		$this->assertEquals('postRestartAction', RequestInfoFactory::getHandlerActionForRequest(\Cundd\Stairtower\Tests\Unit\$this->buildRequest('POST', '/_restart')));
//		$this->assertEquals('postRestartAction', RequestInfoFactory::getHandlerActionForRequest(\Cundd\Stairtower\Tests\Unit\$this->buildRequest('POST', '/_restart/')));
//		$this->assertEquals('postRestartAction', RequestInfoFactory::getHandlerActionForRequest(\Cundd\Stairtower\Tests\Unit\$this->buildRequest('POST', '/_restart/something')));

        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('POST', '/_restart')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('POST', '/_restart/')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest(
                    'POST',
                    '/_restart/something'
                )
            )
        );

        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('GET', '/_restart')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('GET', '/_restart/')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest(
                    'GET',
                    '/_restart/something'
                )
            )
        );

        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('PUT', '/_restart')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('PUT', '/_restart/')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest(
                    'PUT',
                    '/_restart/something'
                )
            )
        );

        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('DELETE', '/_restart')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('DELETE', '/_restart/')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest(
                    'DELETE',
                    '/_restart/something'
                )
            )
        );

        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('HEAD', '/_restart')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('HEAD', '/_restart/')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest(
                    'HEAD',
                    '/_restart/something'
                )
            )
        );


        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('POST', '/restart')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('POST', '/restart/')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest(
                    'POST',
                    '/restart/something'
                )
            )
        );

        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('GET', '/restart')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('GET', '/restart/')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest(
                    'GET',
                    '/restart/something'
                )
            )
        );

        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('PUT', '/restart')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('PUT', '/restart/')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest(
                    'PUT',
                    '/restart/something'
                )
            )
        );

        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('DELETE', '/restart')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('DELETE', '/restart/')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest(
                    'DELETE',
                    '/restart/something'
                )
            )
        );

        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('HEAD', '/restart')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('HEAD', '/restart/')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest(
                    'HEAD',
                    '/restart/something'
                )
            )
        );


        $this->assertEquals(
            'getCountAction',
            RequestInfoFactory::getHandlerActionForRequest($this->buildRequest('GET', '/_count'))
        );
        $this->assertEquals(
            'getCountAction',
            RequestInfoFactory::getHandlerActionForRequest($this->buildRequest('GET', '/_count/'))
        );

        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('POST', '/_count')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('POST', '/_count/')
            )
        );

        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('PUT', '/_count')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('PUT', '/_count/')
            )
        );

        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('DELETE', '/_count')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('DELETE', '/_count/')
            )
        );

        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('HEAD', '/_count')
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('HEAD', '/_count/')
            )
        );


        $this->assertEquals(
            'getCountAction',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('GET', '/database-identifier/_count')
            )
        );
        $this->assertEquals(
            'getCountAction',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest('GET', '/database-identifier/_count/')
            )
        );

        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest(
                    'POST',
                    '/database-identifier/_count'
                )
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest(
                    'POST',
                    '/database-identifier/_count/'
                )
            )
        );

        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest(
                    'PUT',
                    '/database-identifier/_count'
                )
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest(
                    'PUT',
                    '/database-identifier/_count/'
                )
            )
        );

        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest(
                    'DELETE',
                    '/database-identifier/_count'
                )
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest(
                    'DELETE',
                    '/database-identifier/_count/'
                )
            )
        );

        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest(
                    'HEAD',
                    '/database-identifier/_count'
                )
            )
        );
        $this->assertSame(
            '',
            RequestInfoFactory::getHandlerActionForRequest(
                $this->buildRequest(
                    'HEAD',
                    '/database-identifier/_count/'
                )
            )
        );

    }


    /**
     * @test
     */
    public function getHandlerForRequestTest()
    {
        class_alias(
            Handler::class,
            'Cundd\\Stairtower\\Server\\Handler\\SpecialHandler'
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest($this->buildRequest('GET', '/_restart'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest($this->buildRequest('GET', '/_restart/'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(
                $this->buildRequest('GET', '/_restart/something')
            )
        );

        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest($this->buildRequest('HEAD', '/_restart'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest($this->buildRequest('HEAD', '/_restart/'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(
                $this->buildRequest('HEAD', '/_restart/something')
            )
        );

        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest($this->buildRequest('PUT', '/_restart'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest($this->buildRequest('PUT', '/_restart/'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(
                $this->buildRequest('PUT', '/_restart/something')
            )
        );

        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest($this->buildRequest('DELETE', '/_restart'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest($this->buildRequest('DELETE', '/_restart/'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(
                $this->buildRequest('DELETE', '/_restart/something')
            )
        );

        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest($this->buildRequest('POST', '/_restart'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest($this->buildRequest('POST', '/_restart/'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(
                $this->buildRequest('POST', '/_restart/something')
            )
        );


        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest($this->buildRequest('GET', '/_shutdown'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest($this->buildRequest('GET', '/_shutdown/'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(
                $this->buildRequest('GET', '/_shutdown/something')
            )
        );

        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest($this->buildRequest('HEAD', '/_shutdown'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest($this->buildRequest('HEAD', '/_shutdown/'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(
                $this->buildRequest('HEAD', '/_shutdown/something')
            )
        );

        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest($this->buildRequest('PUT', '/_shutdown'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest($this->buildRequest('PUT', '/_shutdown/'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(
                $this->buildRequest('PUT', '/_shutdown/something')
            )
        );

        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest($this->buildRequest('DELETE', '/_shutdown'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest($this->buildRequest('DELETE', '/_shutdown/'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(
                $this->buildRequest('DELETE', '/_shutdown/something')
            )
        );

        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest($this->buildRequest('POST', '/_shutdown'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest($this->buildRequest('POST', '/_shutdown/'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(
                $this->buildRequest('POST', '/_shutdown/something')
            )
        );


        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(
                $this->buildRequest('GET', '/database-identifier')
            )
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(
                $this->buildRequest('GET', '/database-identifier/')
            )
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(
                $this->buildRequest('GET', '/database-identifier/something')
            )
        );

        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(
                $this->buildRequest('HEAD', '/database-identifier')
            )
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(
                $this->buildRequest('HEAD', '/database-identifier/')
            )
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(
                $this->buildRequest('HEAD', '/database-identifier/something')
            )
        );

        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(
                $this->buildRequest('PUT', '/database-identifier')
            )
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(
                $this->buildRequest('PUT', '/database-identifier/')
            )
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(
                $this->buildRequest('PUT', '/database-identifier/something')
            )
        );

        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(
                $this->buildRequest('DELETE', '/database-identifier')
            )
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(
                $this->buildRequest('DELETE', '/database-identifier/')
            )
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(
                $this->buildRequest('DELETE', '/database-identifier/something')
            )
        );

        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(
                $this->buildRequest('POST', '/database-identifier')
            )
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(
                $this->buildRequest('POST', '/database-identifier/')
            )
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(
                $this->buildRequest('POST', '/database-identifier/something')
            )
        );


        $this->assertEquals(
            'Cundd\\Stairtower\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest($this->buildRequest('GET', '/_special'))
        );
        $this->assertEquals(
            'Cundd\\Stairtower\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest($this->buildRequest('GET', '/_special/'))
        );
        $this->assertEquals(
            'Cundd\\Stairtower\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest(
                $this->buildRequest('GET', '/_special/something')
            )
        );

        $this->assertEquals(
            'Cundd\\Stairtower\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest($this->buildRequest('HEAD', '/_special'))
        );
        $this->assertEquals(
            'Cundd\\Stairtower\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest($this->buildRequest('HEAD', '/_special/'))
        );
        $this->assertEquals(
            'Cundd\\Stairtower\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest(
                $this->buildRequest('HEAD', '/_special/something')
            )
        );

        $this->assertEquals(
            'Cundd\\Stairtower\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest($this->buildRequest('PUT', '/_special'))
        );
        $this->assertEquals(
            'Cundd\\Stairtower\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest($this->buildRequest('PUT', '/_special/'))
        );
        $this->assertEquals(
            'Cundd\\Stairtower\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest(
                $this->buildRequest('PUT', '/_special/something')
            )
        );

        $this->assertEquals(
            'Cundd\\Stairtower\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest($this->buildRequest('DELETE', '/_special'))
        );
        $this->assertEquals(
            'Cundd\\Stairtower\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest($this->buildRequest('DELETE', '/_special/'))
        );
        $this->assertEquals(
            'Cundd\\Stairtower\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest(
                $this->buildRequest('DELETE', '/_special/something')
            )
        );

        $this->assertEquals(
            'Cundd\\Stairtower\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest($this->buildRequest('POST', '/_special'))
        );
        $this->assertEquals(
            'Cundd\\Stairtower\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest($this->buildRequest('POST', '/_special/'))
        );
        $this->assertEquals(
            'Cundd\\Stairtower\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest(
                $this->buildRequest('POST', '/_special/something')
            )
        );


        $this->assertEquals(
            'Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest($this->buildRequest('GET', '/_cundd_special'))
        );
        $this->assertEquals(
            'Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest($this->buildRequest('GET', '/_cundd_special/'))
        );
        $this->assertEquals(
            'Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest(
                $this->buildRequest('GET', '/_cundd_special/something')
            )
        );

        $this->assertEquals(
            'Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest($this->buildRequest('HEAD', '/_cundd_special'))
        );
        $this->assertEquals(
            'Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest($this->buildRequest('HEAD', '/_cundd_special/'))
        );
        $this->assertEquals(
            'Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest(
                $this->buildRequest('HEAD', '/_cundd_special/something')
            )
        );

        $this->assertEquals(
            'Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest($this->buildRequest('PUT', '/_cundd_special'))
        );
        $this->assertEquals(
            'Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest($this->buildRequest('PUT', '/_cundd_special/'))
        );
        $this->assertEquals(
            'Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest(
                $this->buildRequest('PUT', '/_cundd_special/something')
            )
        );

        $this->assertEquals(
            'Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest(
                $this->buildRequest('DELETE', '/_cundd_special')
            )
        );
        $this->assertEquals(
            'Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest(
                $this->buildRequest('DELETE', '/_cundd_special/')
            )
        );
        $this->assertEquals(
            'Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest(
                $this->buildRequest('DELETE', '/_cundd_special/something')
            )
        );

        $this->assertEquals(
            'Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest($this->buildRequest('POST', '/_cundd_special'))
        );
        $this->assertEquals(
            'Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest($this->buildRequest('POST', '/_cundd_special/'))
        );
        $this->assertEquals(
            'Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest(
                $this->buildRequest('POST', '/_cundd_special/something')
            )
        );
    }
}
 