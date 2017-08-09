<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Tests\Unit\Server\ValueObject;

use Cundd\Stairtower\Server\ValueObject\RequestInfoFactory;
use Cundd\Stairtower\Tests\Unit\AbstractCase;
use Cundd\Stairtower\Server\Handler\Handler;
use Cundd\Stairtower\Server\Handler\HandlerInterface;
use Cundd\Stairtower\Tests\Fixtures\TestApplication;
use React\Http\Request as BaseRequest;


/**
 * Tests for creating Request objects
 */
class RequestTest extends AbstractCase
{
    /**
     * @var RequestInfoFactory
     */
    protected $requestInfoFactory;


    protected function setUp()
    {
        $this->requestInfoFactory = $this->getDiContainer()->get(RequestInfoFactory::class);
        if (!class_exists('Cundd\\Special\\Application')) {
            class_alias(TestApplication::class, 'Cundd\\Special\\Application');
        }
    }


    /**
     * @test
     */
    public function buildRequestInfoFromRequestTests()
    {
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'GET',
                '/contacts/info@cundd.net'
            )
        );
        $this->assertEquals('GET', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('info@cundd.net', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'HEAD',
                '/contacts/info@cundd.net'
            )
        );
        $this->assertEquals('HEAD', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('info@cundd.net', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'POST',
                '/contacts/info@cundd.net'
            )
        );
        $this->assertEquals('POST', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('info@cundd.net', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'PUT',
                '/contacts/info@cundd.net'
            )
        );
        $this->assertEquals('PUT', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('info@cundd.net', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'DELETE',
                '/contacts/info@cundd.net'
            )
        );
        $this->assertEquals('DELETE', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('info@cundd.net', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());


        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'GET', '/contacts/'
            )
        );
        $this->assertEquals('GET', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertNull($requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'HEAD', '/contacts/'
            )
        );
        $this->assertEquals('HEAD', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertNull($requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'POST', '/contacts/'
            )
        );
        $this->assertEquals('POST', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertNull($requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'PUT', '/contacts/'
            )
        );
        $this->assertEquals('PUT', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertNull($requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'DELETE', '/contacts/'
            )
        );
        $this->assertEquals('DELETE', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertNull($requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());


        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'GET', '/contacts'
            )
        );
        $this->assertEquals('GET', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertNull($requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'HEAD', '/contacts'
            )
        );
        $this->assertEquals('HEAD', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertNull($requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'POST', '/contacts'
            )
        );
        $this->assertEquals('POST', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertNull($requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'PUT', '/contacts'
            )
        );
        $this->assertEquals('PUT', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertNull($requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'DELETE', '/contacts'
            )
        );
        $this->assertEquals('DELETE', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertNull($requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());


        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'GET',
                '/contacts/my-info-path'
            )
        );
        $this->assertEquals('GET', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'HEAD',
                '/contacts/my-info-path'
            )
        );
        $this->assertEquals('HEAD', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'POST',
                '/contacts/my-info-path'
            )
        );
        $this->assertEquals('POST', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'PUT',
                '/contacts/my-info-path'
            )
        );
        $this->assertEquals('PUT', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'DELETE',
                '/contacts/my-info-path'
            )
        );
        $this->assertEquals('DELETE', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());


        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'GET',
                '/contacts-database/my-info-path'
            )
        );
        $this->assertEquals('GET', $requestInfo->getMethod());
        $this->assertEquals('contacts-database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'HEAD',
                '/contacts-database/my-info-path'
            )
        );
        $this->assertEquals('HEAD', $requestInfo->getMethod());
        $this->assertEquals('contacts-database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'POST',
                '/contacts-database/my-info-path'
            )
        );
        $this->assertEquals('POST', $requestInfo->getMethod());
        $this->assertEquals('contacts-database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'PUT',
                '/contacts-database/my-info-path'
            )
        );
        $this->assertEquals('PUT', $requestInfo->getMethod());
        $this->assertEquals('contacts-database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'DELETE',
                '/contacts-database/my-info-path'
            )
        );
        $this->assertEquals('DELETE', $requestInfo->getMethod());
        $this->assertEquals('contacts-database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());


        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'GET',
                '/contacts_database/my_info-path'
            )
        );
        $this->assertEquals('GET', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my_info-path', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'HEAD',
                '/contacts_database/my_info-path'
            )
        );
        $this->assertEquals('HEAD', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my_info-path', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'POST',
                '/contacts_database/my_info-path'
            )
        );
        $this->assertEquals('POST', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my_info-path', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'PUT',
                '/contacts_database/my_info-path'
            )
        );
        $this->assertEquals('PUT', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my_info-path', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'DELETE',
                '/contacts_database/my_info-path'
            )
        );
        $this->assertEquals('DELETE', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my_info-path', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());


        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'GET',
                '/contacts_database/my-super_email@a-smthng.com'
            )
        );
        $this->assertEquals('GET', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'HEAD',
                '/contacts_database/my-super_email@a-smthng.com'
            )
        );
        $this->assertEquals('HEAD', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'POST',
                '/contacts_database/my-super_email@a-smthng.com'
            )
        );
        $this->assertEquals('POST', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'PUT',
                '/contacts_database/my-super_email@a-smthng.com'
            )
        );
        $this->assertEquals('PUT', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'DELETE',
                '/contacts_database/my-super_email@a-smthng.com'
            )
        );
        $this->assertEquals('DELETE', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());


        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'GET',
                '/contacts_database/my-super_email@a-smthng.com/something-more'
            )
        );
        $this->assertEquals('GET', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'HEAD',
                '/contacts_database/my-super_email@a-smthng.com/something-more'
            )
        );
        $this->assertEquals('HEAD', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'POST',
                '/contacts_database/my-super_email@a-smthng.com/something-more'
            )
        );
        $this->assertEquals('POST', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'PUT',
                '/contacts_database/my-super_email@a-smthng.com/something-more'
            )
        );
        $this->assertEquals('PUT', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'DELETE',
                '/contacts_database/my-super_email@a-smthng.com/something-more'
            )
        );
        $this->assertEquals('DELETE', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());
    }

    /**
     * @test
     */
    public function buildRequestInfoFromRequestWithControllerTest()
    {
        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
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
            new BaseRequest(
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
            new BaseRequest(
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
            new BaseRequest(
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
            new BaseRequest(
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
            new BaseRequest(
                'GET',
                '/_cundd-notexistingtest-application/my_undefined_method'
            )
        );
        $this->assertEquals('GET', $requestInfo->getMethod());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my_undefined_method', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'HEAD',
                '/_cundd-notexistingtest-application/my_undefined_method'
            )
        );
        $this->assertEquals('HEAD', $requestInfo->getMethod());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my_undefined_method', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'POST',
                '/_cundd-notexistingtest-application/my_undefined_method'
            )
        );
        $this->assertEquals('POST', $requestInfo->getMethod());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my_undefined_method', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'PUT',
                '/_cundd-notexistingtest-application/my_undefined_method'
            )
        );
        $this->assertEquals('PUT', $requestInfo->getMethod());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my_undefined_method', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
                'DELETE',
                '/_cundd-notexistingtest-application/my_undefined_method'
            )
        );
        $this->assertEquals('DELETE', $requestInfo->getMethod());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my_undefined_method', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());


        $requestInfo = $this->requestInfoFactory->buildRequestFromRawRequest(
            new BaseRequest(
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
            new BaseRequest(
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
            new BaseRequest(
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
            new BaseRequest(
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
            new BaseRequest(
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
            new BaseRequest(
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
            new BaseRequest(
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
            new BaseRequest(
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
            new BaseRequest(
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
            new BaseRequest(
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
            new BaseRequest(
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
            new BaseRequest(
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
            new BaseRequest(
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
            new BaseRequest(
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
            new BaseRequest(
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
            new BaseRequest(
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
            new BaseRequest(
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
            new BaseRequest(
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
            new BaseRequest(
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
            new BaseRequest(
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
            new BaseRequest(
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
            new BaseRequest(
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
            new BaseRequest(
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
            new BaseRequest(
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
            new BaseRequest(
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
            new BaseRequest(
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
            new BaseRequest(
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
            new BaseRequest(
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
            new BaseRequest(
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
            new BaseRequest(
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
            new BaseRequest(
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
            new BaseRequest(
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
            new BaseRequest(
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
            new BaseRequest(
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
            new BaseRequest(
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
        $this->assertFalse(RequestInfoFactory::getServerActionForRequest(new BaseRequest('GET', '/_restart')));
        $this->assertFalse(RequestInfoFactory::getServerActionForRequest(new BaseRequest('GET', '/_restart/')));
        $this->assertFalse(
            RequestInfoFactory::getServerActionForRequest(
                new BaseRequest(
                    'GET',
                    '/_restart/something'
                )
            )
        );

        $this->assertFalse(RequestInfoFactory::getServerActionForRequest(new BaseRequest('HEAD', '/_restart')));
        $this->assertFalse(RequestInfoFactory::getServerActionForRequest(new BaseRequest('HEAD', '/_restart/')));
        $this->assertFalse(
            RequestInfoFactory::getServerActionForRequest(
                new BaseRequest(
                    'HEAD',
                    '/_restart/something'
                )
            )
        );

        $this->assertFalse(RequestInfoFactory::getServerActionForRequest(new BaseRequest('PUT', '/_restart')));
        $this->assertFalse(RequestInfoFactory::getServerActionForRequest(new BaseRequest('PUT', '/_restart/')));
        $this->assertFalse(
            RequestInfoFactory::getServerActionForRequest(
                new BaseRequest(
                    'PUT',
                    '/_restart/something'
                )
            )
        );

        $this->assertFalse(RequestInfoFactory::getServerActionForRequest(new BaseRequest('DELETE', '/_restart')));
        $this->assertFalse(RequestInfoFactory::getServerActionForRequest(new BaseRequest('DELETE', '/_restart/')));
        $this->assertFalse(
            RequestInfoFactory::getServerActionForRequest(
                new BaseRequest(
                    'DELETE',
                    '/_restart/something'
                )
            )
        );

        $this->assertEquals(
            'restart',
            RequestInfoFactory::getServerActionForRequest(new BaseRequest('POST', '/_restart'))
        );
        $this->assertEquals(
            'restart',
            RequestInfoFactory::getServerActionForRequest(new BaseRequest('POST', '/_restart/'))
        );
        $this->assertEquals(
            'restart',
            RequestInfoFactory::getServerActionForRequest(new BaseRequest('POST', '/_restart/something'))
        );


        $this->assertFalse(RequestInfoFactory::getServerActionForRequest(new BaseRequest('GET', '/_shutdown')));
        $this->assertFalse(RequestInfoFactory::getServerActionForRequest(new BaseRequest('GET', '/_shutdown/')));
        $this->assertFalse(
            RequestInfoFactory::getServerActionForRequest(
                new BaseRequest(
                    'GET',
                    '/_shutdown/something'
                )
            )
        );

        $this->assertFalse(RequestInfoFactory::getServerActionForRequest(new BaseRequest('HEAD', '/_shutdown')));
        $this->assertFalse(RequestInfoFactory::getServerActionForRequest(new BaseRequest('HEAD', '/_shutdown/')));
        $this->assertFalse(
            RequestInfoFactory::getServerActionForRequest(
                new BaseRequest(
                    'HEAD',
                    '/_shutdown/something'
                )
            )
        );

        $this->assertFalse(RequestInfoFactory::getServerActionForRequest(new BaseRequest('PUT', '/_shutdown')));
        $this->assertFalse(RequestInfoFactory::getServerActionForRequest(new BaseRequest('PUT', '/_shutdown/')));
        $this->assertFalse(
            RequestInfoFactory::getServerActionForRequest(
                new BaseRequest(
                    'PUT',
                    '/_shutdown/something'
                )
            )
        );

        $this->assertFalse(RequestInfoFactory::getServerActionForRequest(new BaseRequest('DELETE', '/_shutdown')));
        $this->assertFalse(RequestInfoFactory::getServerActionForRequest(new BaseRequest('DELETE', '/_shutdown/')));
        $this->assertFalse(
            RequestInfoFactory::getServerActionForRequest(
                new BaseRequest(
                    'DELETE',
                    '/_shutdown/something'
                )
            )
        );

        $this->assertEquals(
            'shutdown',
            RequestInfoFactory::getServerActionForRequest(new BaseRequest('POST', '/_shutdown'))
        );
        $this->assertEquals(
            'shutdown',
            RequestInfoFactory::getServerActionForRequest(new BaseRequest('POST', '/_shutdown/'))
        );
        $this->assertEquals(
            'shutdown',
            RequestInfoFactory::getServerActionForRequest(new BaseRequest('POST', '/_shutdown/something'))
        );


//		$this->assertFalse(RequestInfoFactory::getServerActionForRequest(new BaseRequest('GET', '/_stop')));
//		$this->assertFalse(RequestInfoFactory::getServerActionForRequest(new BaseRequest('GET', '/_stop/')));
//		$this->assertFalse(RequestInfoFactory::getServerActionForRequest(new BaseRequest('GET', '/_stop/something')));
//
//		$this->assertFalse(RequestInfoFactory::getServerActionForRequest(new BaseRequest('HEAD', '/_stop')));
//		$this->assertFalse(RequestInfoFactory::getServerActionForRequest(new BaseRequest('HEAD', '/_stop/')));
//		$this->assertFalse(RequestInfoFactory::getServerActionForRequest(new BaseRequest('HEAD', '/_stop/something')));
//
//		$this->assertFalse(RequestInfoFactory::getServerActionForRequest(new BaseRequest('PUT', '/_stop')));
//		$this->assertFalse(RequestInfoFactory::getServerActionForRequest(new BaseRequest('PUT', '/_stop/')));
//		$this->assertFalse(RequestInfoFactory::getServerActionForRequest(new BaseRequest('PUT', '/_stop/something')));
//
//		$this->assertFalse(RequestInfoFactory::getServerActionForRequest(new BaseRequest('DELETE', '/_stop')));
//		$this->assertFalse(RequestInfoFactory::getServerActionForRequest(new BaseRequest('DELETE', '/_stop/')));
//		$this->assertFalse(RequestInfoFactory::getServerActionForRequest(new BaseRequest('DELETE', '/_stop/something')));
//
//		$this->assertEquals('stop', RequestInfoFactory::getServerActionForRequest(new BaseRequest('POST', '/_stop')));
//		$this->assertEquals('stop', RequestInfoFactory::getServerActionForRequest(new BaseRequest('POST', '/_stop/')));
//		$this->assertEquals('stop', RequestInfoFactory::getServerActionForRequest(new BaseRequest('POST', '/_stop/something')));

    }

    /**
     * @test
     */
    public function getHandlerActionForRequestTest()
    {
        $this->assertEquals(
            'getStatsAction',
            RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('GET', '/_stats'))
        );
        $this->assertEquals(
            'getStatsAction',
            RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('GET', '/_stats/'))
        );
        $this->assertEquals(
            'getStatsAction',
            RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('GET', '/_stats/something'))
        );

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('POST', '/_stats')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('POST', '/_stats/')));
        $this->assertFalse(
            RequestInfoFactory::getHandlerActionForRequest(
                new BaseRequest(
                    'POST',
                    '/_stats/something'
                )
            )
        );

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('PUT', '/_stats')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('PUT', '/_stats/')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('PUT', '/_stats/something')));

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('DELETE', '/_stats')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('DELETE', '/_stats/')));
        $this->assertFalse(
            RequestInfoFactory::getHandlerActionForRequest(
                new BaseRequest(
                    'DELETE',
                    '/_stats/something'
                )
            )
        );

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('HEAD', '/_stats')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('HEAD', '/_stats/')));
        $this->assertFalse(
            RequestInfoFactory::getHandlerActionForRequest(
                new BaseRequest(
                    'HEAD',
                    '/_stats/something'
                )
            )
        );


        $this->assertEquals(
            'getAllDbsAction',
            RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('GET', '/_all_dbs'))
        );
        $this->assertEquals(
            'getAllDbsAction',
            RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('GET', '/_all_dbs/'))
        );

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('POST', '/_all_dbs')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('POST', '/_all_dbs/')));

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('PUT', '/_all_dbs')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('PUT', '/_all_dbs/')));

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('DELETE', '/_all_dbs')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('DELETE', '/_all_dbs/')));

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('HEAD', '/_all_dbs')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('HEAD', '/_all_dbs/')));


//		$this->assertEquals('postRestartAction', RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('POST', '/_restart')));
//		$this->assertEquals('postRestartAction', RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('POST', '/_restart/')));
//		$this->assertEquals('postRestartAction', RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('POST', '/_restart/something')));

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('POST', '/_restart')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('POST', '/_restart/')));
        $this->assertFalse(
            RequestInfoFactory::getHandlerActionForRequest(
                new BaseRequest(
                    'POST',
                    '/_restart/something'
                )
            )
        );

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('GET', '/_restart')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('GET', '/_restart/')));
        $this->assertFalse(
            RequestInfoFactory::getHandlerActionForRequest(
                new BaseRequest(
                    'GET',
                    '/_restart/something'
                )
            )
        );

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('PUT', '/_restart')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('PUT', '/_restart/')));
        $this->assertFalse(
            RequestInfoFactory::getHandlerActionForRequest(
                new BaseRequest(
                    'PUT',
                    '/_restart/something'
                )
            )
        );

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('DELETE', '/_restart')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('DELETE', '/_restart/')));
        $this->assertFalse(
            RequestInfoFactory::getHandlerActionForRequest(
                new BaseRequest(
                    'DELETE',
                    '/_restart/something'
                )
            )
        );

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('HEAD', '/_restart')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('HEAD', '/_restart/')));
        $this->assertFalse(
            RequestInfoFactory::getHandlerActionForRequest(
                new BaseRequest(
                    'HEAD',
                    '/_restart/something'
                )
            )
        );


        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('POST', '/restart')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('POST', '/restart/')));
        $this->assertFalse(
            RequestInfoFactory::getHandlerActionForRequest(
                new BaseRequest(
                    'POST',
                    '/restart/something'
                )
            )
        );

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('GET', '/restart')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('GET', '/restart/')));
        $this->assertFalse(
            RequestInfoFactory::getHandlerActionForRequest(
                new BaseRequest(
                    'GET',
                    '/restart/something'
                )
            )
        );

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('PUT', '/restart')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('PUT', '/restart/')));
        $this->assertFalse(
            RequestInfoFactory::getHandlerActionForRequest(
                new BaseRequest(
                    'PUT',
                    '/restart/something'
                )
            )
        );

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('DELETE', '/restart')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('DELETE', '/restart/')));
        $this->assertFalse(
            RequestInfoFactory::getHandlerActionForRequest(
                new BaseRequest(
                    'DELETE',
                    '/restart/something'
                )
            )
        );

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('HEAD', '/restart')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('HEAD', '/restart/')));
        $this->assertFalse(
            RequestInfoFactory::getHandlerActionForRequest(
                new BaseRequest(
                    'HEAD',
                    '/restart/something'
                )
            )
        );


        $this->assertEquals(
            'getCountAction',
            RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('GET', '/_count'))
        );
        $this->assertEquals(
            'getCountAction',
            RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('GET', '/_count/'))
        );

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('POST', '/_count')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('POST', '/_count/')));

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('PUT', '/_count')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('PUT', '/_count/')));

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('DELETE', '/_count')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('DELETE', '/_count/')));

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('HEAD', '/_count')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('HEAD', '/_count/')));


        $this->assertEquals(
            'getCountAction',
            RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('GET', '/database-identifier/_count'))
        );
        $this->assertEquals(
            'getCountAction',
            RequestInfoFactory::getHandlerActionForRequest(new BaseRequest('GET', '/database-identifier/_count/'))
        );

        $this->assertFalse(
            RequestInfoFactory::getHandlerActionForRequest(
                new BaseRequest(
                    'POST',
                    '/database-identifier/_count'
                )
            )
        );
        $this->assertFalse(
            RequestInfoFactory::getHandlerActionForRequest(
                new BaseRequest(
                    'POST',
                    '/database-identifier/_count/'
                )
            )
        );

        $this->assertFalse(
            RequestInfoFactory::getHandlerActionForRequest(
                new BaseRequest(
                    'PUT',
                    '/database-identifier/_count'
                )
            )
        );
        $this->assertFalse(
            RequestInfoFactory::getHandlerActionForRequest(
                new BaseRequest(
                    'PUT',
                    '/database-identifier/_count/'
                )
            )
        );

        $this->assertFalse(
            RequestInfoFactory::getHandlerActionForRequest(
                new BaseRequest(
                    'DELETE',
                    '/database-identifier/_count'
                )
            )
        );
        $this->assertFalse(
            RequestInfoFactory::getHandlerActionForRequest(
                new BaseRequest(
                    'DELETE',
                    '/database-identifier/_count/'
                )
            )
        );

        $this->assertFalse(
            RequestInfoFactory::getHandlerActionForRequest(
                new BaseRequest(
                    'HEAD',
                    '/database-identifier/_count'
                )
            )
        );
        $this->assertFalse(
            RequestInfoFactory::getHandlerActionForRequest(
                new BaseRequest(
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
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('GET', '/_restart'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('GET', '/_restart/'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('GET', '/_restart/something'))
        );

        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('HEAD', '/_restart'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('HEAD', '/_restart/'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('HEAD', '/_restart/something'))
        );

        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('PUT', '/_restart'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('PUT', '/_restart/'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('PUT', '/_restart/something'))
        );

        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('DELETE', '/_restart'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('DELETE', '/_restart/'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('DELETE', '/_restart/something'))
        );

        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('POST', '/_restart'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('POST', '/_restart/'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('POST', '/_restart/something'))
        );


        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('GET', '/_shutdown'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('GET', '/_shutdown/'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('GET', '/_shutdown/something'))
        );

        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('HEAD', '/_shutdown'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('HEAD', '/_shutdown/'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('HEAD', '/_shutdown/something'))
        );

        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('PUT', '/_shutdown'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('PUT', '/_shutdown/'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('PUT', '/_shutdown/something'))
        );

        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('DELETE', '/_shutdown'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('DELETE', '/_shutdown/'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('DELETE', '/_shutdown/something'))
        );

        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('POST', '/_shutdown'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('POST', '/_shutdown/'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('POST', '/_shutdown/something'))
        );


        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('GET', '/database-identifier'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('GET', '/database-identifier/'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('GET', '/database-identifier/something'))
        );

        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('HEAD', '/database-identifier'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('HEAD', '/database-identifier/'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('HEAD', '/database-identifier/something'))
        );

        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('PUT', '/database-identifier'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('PUT', '/database-identifier/'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('PUT', '/database-identifier/something'))
        );

        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('DELETE', '/database-identifier'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('DELETE', '/database-identifier/'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('DELETE', '/database-identifier/something'))
        );

        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('POST', '/database-identifier'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('POST', '/database-identifier/'))
        );
        $this->assertEquals(
            HandlerInterface::class,
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('POST', '/database-identifier/something'))
        );


        $this->assertEquals(
            'Cundd\\Stairtower\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('GET', '/_special'))
        );
        $this->assertEquals(
            'Cundd\\Stairtower\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('GET', '/_special/'))
        );
        $this->assertEquals(
            'Cundd\\Stairtower\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('GET', '/_special/something'))
        );

        $this->assertEquals(
            'Cundd\\Stairtower\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('HEAD', '/_special'))
        );
        $this->assertEquals(
            'Cundd\\Stairtower\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('HEAD', '/_special/'))
        );
        $this->assertEquals(
            'Cundd\\Stairtower\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('HEAD', '/_special/something'))
        );

        $this->assertEquals(
            'Cundd\\Stairtower\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('PUT', '/_special'))
        );
        $this->assertEquals(
            'Cundd\\Stairtower\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('PUT', '/_special/'))
        );
        $this->assertEquals(
            'Cundd\\Stairtower\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('PUT', '/_special/something'))
        );

        $this->assertEquals(
            'Cundd\\Stairtower\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('DELETE', '/_special'))
        );
        $this->assertEquals(
            'Cundd\\Stairtower\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('DELETE', '/_special/'))
        );
        $this->assertEquals(
            'Cundd\\Stairtower\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('DELETE', '/_special/something'))
        );

        $this->assertEquals(
            'Cundd\\Stairtower\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('POST', '/_special'))
        );
        $this->assertEquals(
            'Cundd\\Stairtower\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('POST', '/_special/'))
        );
        $this->assertEquals(
            'Cundd\\Stairtower\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('POST', '/_special/something'))
        );


        $this->assertEquals(
            'Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('GET', '/_cundd_special'))
        );
        $this->assertEquals(
            'Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('GET', '/_cundd_special/'))
        );
        $this->assertEquals(
            'Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('GET', '/_cundd_special/something'))
        );

        $this->assertEquals(
            'Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('HEAD', '/_cundd_special'))
        );
        $this->assertEquals(
            'Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('HEAD', '/_cundd_special/'))
        );
        $this->assertEquals(
            'Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('HEAD', '/_cundd_special/something'))
        );

        $this->assertEquals(
            'Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('PUT', '/_cundd_special'))
        );
        $this->assertEquals(
            'Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('PUT', '/_cundd_special/'))
        );
        $this->assertEquals(
            'Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('PUT', '/_cundd_special/something'))
        );

        $this->assertEquals(
            'Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('DELETE', '/_cundd_special'))
        );
        $this->assertEquals(
            'Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('DELETE', '/_cundd_special/'))
        );
        $this->assertEquals(
            'Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('DELETE', '/_cundd_special/something'))
        );

        $this->assertEquals(
            'Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('POST', '/_cundd_special'))
        );
        $this->assertEquals(
            'Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('POST', '/_cundd_special/'))
        );
        $this->assertEquals(
            'Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest(new BaseRequest('POST', '/_cundd_special/something'))
        );
    }
}
 