<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 11.10.14
 * Time: 20:20
 */

namespace Cundd\PersistentObjectStore\Server\ValueObject;

use Cundd\PersistentObjectStore\Server\Handler\Handler;
use React\Http\Request;

class SpecialHandler extends Handler
{
}

class TestApplication
{
}

class Test_Application_Controller
{
    public function getMyMethodAction()
    {
    }

    public function postMyMethodAction()
    {
    }

    public function deleteMyMethodAction()
    {
    }

    public function putMyMethodAction()
    {
    }

    public function headMyMethodAction()
    {
    }
}

/**
 * Tests for creating RequestInfo objects
 *
 * @package Cundd\PersistentObjectStore\Server\ValueObject
 */
class RequestInfoTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        class_alias('Cundd\\PersistentObjectStore\\Server\\ValueObject\\TestApplication',
            'Cundd\\Special\\Application');
        class_alias('Cundd\\PersistentObjectStore\\Server\\ValueObject\\Test_Application_Controller',
            'Cundd\\Test\\ApplicationController');
    }


    /**
     * @test
     */
    public function buildRequestInfoFromRequestTests()
    {
        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET', '/contacts/info@cundd.net'));
        $this->assertEquals('GET', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('info@cundd.net', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('HEAD', '/contacts/info@cundd.net'));
        $this->assertEquals('HEAD', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('info@cundd.net', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('POST', '/contacts/info@cundd.net'));
        $this->assertEquals('POST', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('info@cundd.net', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('PUT', '/contacts/info@cundd.net'));
        $this->assertEquals('PUT', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('info@cundd.net', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('DELETE',
            '/contacts/info@cundd.net'));
        $this->assertEquals('DELETE', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('info@cundd.net', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());


        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET', '/contacts/'));
        $this->assertEquals('GET', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertNull($requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('HEAD', '/contacts/'));
        $this->assertEquals('HEAD', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertNull($requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('POST', '/contacts/'));
        $this->assertEquals('POST', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertNull($requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('PUT', '/contacts/'));
        $this->assertEquals('PUT', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertNull($requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('DELETE', '/contacts/'));
        $this->assertEquals('DELETE', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertNull($requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());


        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET', '/contacts'));
        $this->assertEquals('GET', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertNull($requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('HEAD', '/contacts'));
        $this->assertEquals('HEAD', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertNull($requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('POST', '/contacts'));
        $this->assertEquals('POST', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertNull($requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('PUT', '/contacts'));
        $this->assertEquals('PUT', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertNull($requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('DELETE', '/contacts'));
        $this->assertEquals('DELETE', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertNull($requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());


        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET', '/contacts/my-info-path'));
        $this->assertEquals('GET', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('HEAD', '/contacts/my-info-path'));
        $this->assertEquals('HEAD', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('POST', '/contacts/my-info-path'));
        $this->assertEquals('POST', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('PUT', '/contacts/my-info-path'));
        $this->assertEquals('PUT', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('DELETE', '/contacts/my-info-path'));
        $this->assertEquals('DELETE', $requestInfo->getMethod());
        $this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());


        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET',
            '/contacts-database/my-info-path'));
        $this->assertEquals('GET', $requestInfo->getMethod());
        $this->assertEquals('contacts-database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('HEAD',
            '/contacts-database/my-info-path'));
        $this->assertEquals('HEAD', $requestInfo->getMethod());
        $this->assertEquals('contacts-database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('POST',
            '/contacts-database/my-info-path'));
        $this->assertEquals('POST', $requestInfo->getMethod());
        $this->assertEquals('contacts-database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('PUT',
            '/contacts-database/my-info-path'));
        $this->assertEquals('PUT', $requestInfo->getMethod());
        $this->assertEquals('contacts-database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('DELETE',
            '/contacts-database/my-info-path'));
        $this->assertEquals('DELETE', $requestInfo->getMethod());
        $this->assertEquals('contacts-database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());


        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET',
            '/contacts_database/my_info-path'));
        $this->assertEquals('GET', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my_info-path', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('HEAD',
            '/contacts_database/my_info-path'));
        $this->assertEquals('HEAD', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my_info-path', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('POST',
            '/contacts_database/my_info-path'));
        $this->assertEquals('POST', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my_info-path', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('PUT',
            '/contacts_database/my_info-path'));
        $this->assertEquals('PUT', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my_info-path', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('DELETE',
            '/contacts_database/my_info-path'));
        $this->assertEquals('DELETE', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my_info-path', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());


        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET',
            '/contacts_database/my-super_email@a-smthng.com'));
        $this->assertEquals('GET', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('HEAD',
            '/contacts_database/my-super_email@a-smthng.com'));
        $this->assertEquals('HEAD', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('POST',
            '/contacts_database/my-super_email@a-smthng.com'));
        $this->assertEquals('POST', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('PUT',
            '/contacts_database/my-super_email@a-smthng.com'));
        $this->assertEquals('PUT', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('DELETE',
            '/contacts_database/my-super_email@a-smthng.com'));
        $this->assertEquals('DELETE', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());


        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET',
            '/contacts_database/my-super_email@a-smthng.com/something-more'));
        $this->assertEquals('GET', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('HEAD',
            '/contacts_database/my-super_email@a-smthng.com/something-more'));
        $this->assertEquals('HEAD', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('POST',
            '/contacts_database/my-super_email@a-smthng.com/something-more'));
        $this->assertEquals('POST', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('PUT',
            '/contacts_database/my-super_email@a-smthng.com/something-more'));
        $this->assertEquals('PUT', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('DELETE',
            '/contacts_database/my-super_email@a-smthng.com/something-more'));
        $this->assertEquals('DELETE', $requestInfo->getMethod());
        $this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());
    }

    /**
     * @test
     */
    public function buildRequestInfoFromRequestWithControllerTest()
    {
        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET',
            '/_cundd_test_application/my_method'));
        $this->assertEquals('GET', $requestInfo->getMethod());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertEquals('getMyMethodAction', $requestInfo->getSpecialHandlerAction());
        $this->assertEquals('getMyMethodAction', $requestInfo->getAction());
        $this->assertEquals('Cundd\\Test\\ApplicationController', $requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('HEAD',
            '/_cundd_test_application/my_method'));
        $this->assertEquals('HEAD', $requestInfo->getMethod());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertEquals('headMyMethodAction', $requestInfo->getSpecialHandlerAction());
        $this->assertEquals('headMyMethodAction', $requestInfo->getAction());
        $this->assertEquals('Cundd\\Test\\ApplicationController', $requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('POST',
            '/_cundd_test_application/my_method'));
        $this->assertEquals('POST', $requestInfo->getMethod());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertEquals('postMyMethodAction', $requestInfo->getSpecialHandlerAction());
        $this->assertEquals('postMyMethodAction', $requestInfo->getAction());
        $this->assertEquals('Cundd\\Test\\ApplicationController', $requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('PUT',
            '/_cundd_test_application/my_method'));
        $this->assertEquals('PUT', $requestInfo->getMethod());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertEquals('putMyMethodAction', $requestInfo->getSpecialHandlerAction());
        $this->assertEquals('putMyMethodAction', $requestInfo->getAction());
        $this->assertEquals('Cundd\\Test\\ApplicationController', $requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('DELETE',
            '/_cundd_test_application/my_method'));
        $this->assertEquals('DELETE', $requestInfo->getMethod());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertEquals('deleteMyMethodAction', $requestInfo->getSpecialHandlerAction());
        $this->assertEquals('deleteMyMethodAction', $requestInfo->getAction());
        $this->assertEquals('Cundd\\Test\\ApplicationController', $requestInfo->getControllerClass());


        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET',
            '/_cundd_notexistingtest_application/my_undefined_method'));
        $this->assertEquals('GET', $requestInfo->getMethod());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my_undefined_method', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('HEAD',
            '/_cundd_notexistingtest_application/my_undefined_method'));
        $this->assertEquals('HEAD', $requestInfo->getMethod());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my_undefined_method', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('POST',
            '/_cundd_notexistingtest_application/my_undefined_method'));
        $this->assertEquals('POST', $requestInfo->getMethod());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my_undefined_method', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('PUT',
            '/_cundd_notexistingtest_application/my_undefined_method'));
        $this->assertEquals('PUT', $requestInfo->getMethod());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my_undefined_method', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('DELETE',
            '/_cundd_notexistingtest_application/my_undefined_method'));
        $this->assertEquals('DELETE', $requestInfo->getMethod());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('my_undefined_method', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertNull($requestInfo->getSpecialHandlerAction());
        $this->assertNull($requestInfo->getAction());
        $this->assertNull($requestInfo->getControllerClass());


        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET',
            '/_cundd_test_application/my_undefined_method'));
        $this->assertEquals('GET', $requestInfo->getMethod());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertEquals('getMyUndefinedMethodAction', $requestInfo->getSpecialHandlerAction());
        $this->assertEquals('getMyUndefinedMethodAction', $requestInfo->getAction());
        $this->assertEquals('Cundd\\Test\\ApplicationController', $requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('HEAD',
            '/_cundd_test_application/my_undefined_method'));
        $this->assertEquals('HEAD', $requestInfo->getMethod());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('', $requestInfo->getDataIdentifier());
        $this->assertTrue($requestInfo->isReadRequest());
        $this->assertFalse($requestInfo->isWriteRequest());
        $this->assertEquals('headMyUndefinedMethodAction', $requestInfo->getSpecialHandlerAction());
        $this->assertEquals('headMyUndefinedMethodAction', $requestInfo->getAction());
        $this->assertEquals('Cundd\\Test\\ApplicationController', $requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('POST',
            '/_cundd_test_application/my_undefined_method'));
        $this->assertEquals('POST', $requestInfo->getMethod());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertEquals('postMyUndefinedMethodAction', $requestInfo->getSpecialHandlerAction());
        $this->assertEquals('postMyUndefinedMethodAction', $requestInfo->getAction());
        $this->assertEquals('Cundd\\Test\\ApplicationController', $requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('PUT',
            '/_cundd_test_application/my_undefined_method'));
        $this->assertEquals('PUT', $requestInfo->getMethod());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertEquals('putMyUndefinedMethodAction', $requestInfo->getSpecialHandlerAction());
        $this->assertEquals('putMyUndefinedMethodAction', $requestInfo->getAction());
        $this->assertEquals('Cundd\\Test\\ApplicationController', $requestInfo->getControllerClass());

        $requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('DELETE',
            '/_cundd_test_application/my_undefined_method'));
        $this->assertEquals('DELETE', $requestInfo->getMethod());
        $this->assertEquals('', $requestInfo->getDatabaseIdentifier());
        $this->assertEquals('', $requestInfo->getDataIdentifier());
        $this->assertFalse($requestInfo->isReadRequest());
        $this->assertTrue($requestInfo->isWriteRequest());
        $this->assertEquals('deleteMyUndefinedMethodAction', $requestInfo->getSpecialHandlerAction());
        $this->assertEquals('deleteMyUndefinedMethodAction', $requestInfo->getAction());
        $this->assertEquals('Cundd\\Test\\ApplicationController', $requestInfo->getControllerClass());
    }

    /**
     * @test
     */
    public function getServerActionForRequestTest()
    {
        $this->assertFalse(RequestInfoFactory::getServerActionForRequest(new Request('GET', '/_restart')));
        $this->assertFalse(RequestInfoFactory::getServerActionForRequest(new Request('GET', '/_restart/')));
        $this->assertFalse(RequestInfoFactory::getServerActionForRequest(new Request('GET', '/_restart/something')));

        $this->assertFalse(RequestInfoFactory::getServerActionForRequest(new Request('HEAD', '/_restart')));
        $this->assertFalse(RequestInfoFactory::getServerActionForRequest(new Request('HEAD', '/_restart/')));
        $this->assertFalse(RequestInfoFactory::getServerActionForRequest(new Request('HEAD', '/_restart/something')));

        $this->assertFalse(RequestInfoFactory::getServerActionForRequest(new Request('PUT', '/_restart')));
        $this->assertFalse(RequestInfoFactory::getServerActionForRequest(new Request('PUT', '/_restart/')));
        $this->assertFalse(RequestInfoFactory::getServerActionForRequest(new Request('PUT', '/_restart/something')));

        $this->assertFalse(RequestInfoFactory::getServerActionForRequest(new Request('DELETE', '/_restart')));
        $this->assertFalse(RequestInfoFactory::getServerActionForRequest(new Request('DELETE', '/_restart/')));
        $this->assertFalse(RequestInfoFactory::getServerActionForRequest(new Request('DELETE', '/_restart/something')));

        $this->assertEquals('restart', RequestInfoFactory::getServerActionForRequest(new Request('POST', '/_restart')));
        $this->assertEquals('restart',
            RequestInfoFactory::getServerActionForRequest(new Request('POST', '/_restart/')));
        $this->assertEquals('restart',
            RequestInfoFactory::getServerActionForRequest(new Request('POST', '/_restart/something')));


        $this->assertFalse(RequestInfoFactory::getServerActionForRequest(new Request('GET', '/_shutdown')));
        $this->assertFalse(RequestInfoFactory::getServerActionForRequest(new Request('GET', '/_shutdown/')));
        $this->assertFalse(RequestInfoFactory::getServerActionForRequest(new Request('GET', '/_shutdown/something')));

        $this->assertFalse(RequestInfoFactory::getServerActionForRequest(new Request('HEAD', '/_shutdown')));
        $this->assertFalse(RequestInfoFactory::getServerActionForRequest(new Request('HEAD', '/_shutdown/')));
        $this->assertFalse(RequestInfoFactory::getServerActionForRequest(new Request('HEAD', '/_shutdown/something')));

        $this->assertFalse(RequestInfoFactory::getServerActionForRequest(new Request('PUT', '/_shutdown')));
        $this->assertFalse(RequestInfoFactory::getServerActionForRequest(new Request('PUT', '/_shutdown/')));
        $this->assertFalse(RequestInfoFactory::getServerActionForRequest(new Request('PUT', '/_shutdown/something')));

        $this->assertFalse(RequestInfoFactory::getServerActionForRequest(new Request('DELETE', '/_shutdown')));
        $this->assertFalse(RequestInfoFactory::getServerActionForRequest(new Request('DELETE', '/_shutdown/')));
        $this->assertFalse(RequestInfoFactory::getServerActionForRequest(new Request('DELETE',
            '/_shutdown/something')));

        $this->assertEquals('shutdown',
            RequestInfoFactory::getServerActionForRequest(new Request('POST', '/_shutdown')));
        $this->assertEquals('shutdown',
            RequestInfoFactory::getServerActionForRequest(new Request('POST', '/_shutdown/')));
        $this->assertEquals('shutdown',
            RequestInfoFactory::getServerActionForRequest(new Request('POST', '/_shutdown/something')));


//		$this->assertFalse(RequestInfoFactory::getServerActionForRequest(new Request('GET', '/_stop')));
//		$this->assertFalse(RequestInfoFactory::getServerActionForRequest(new Request('GET', '/_stop/')));
//		$this->assertFalse(RequestInfoFactory::getServerActionForRequest(new Request('GET', '/_stop/something')));
//
//		$this->assertFalse(RequestInfoFactory::getServerActionForRequest(new Request('HEAD', '/_stop')));
//		$this->assertFalse(RequestInfoFactory::getServerActionForRequest(new Request('HEAD', '/_stop/')));
//		$this->assertFalse(RequestInfoFactory::getServerActionForRequest(new Request('HEAD', '/_stop/something')));
//
//		$this->assertFalse(RequestInfoFactory::getServerActionForRequest(new Request('PUT', '/_stop')));
//		$this->assertFalse(RequestInfoFactory::getServerActionForRequest(new Request('PUT', '/_stop/')));
//		$this->assertFalse(RequestInfoFactory::getServerActionForRequest(new Request('PUT', '/_stop/something')));
//
//		$this->assertFalse(RequestInfoFactory::getServerActionForRequest(new Request('DELETE', '/_stop')));
//		$this->assertFalse(RequestInfoFactory::getServerActionForRequest(new Request('DELETE', '/_stop/')));
//		$this->assertFalse(RequestInfoFactory::getServerActionForRequest(new Request('DELETE', '/_stop/something')));
//
//		$this->assertEquals('stop', RequestInfoFactory::getServerActionForRequest(new Request('POST', '/_stop')));
//		$this->assertEquals('stop', RequestInfoFactory::getServerActionForRequest(new Request('POST', '/_stop/')));
//		$this->assertEquals('stop', RequestInfoFactory::getServerActionForRequest(new Request('POST', '/_stop/something')));

    }

    /**
     * @test
     */
    public function getHandlerActionForRequestTest()
    {
        $this->assertEquals('getStatsAction',
            RequestInfoFactory::getHandlerActionForRequest(new Request('GET', '/_stats')));
        $this->assertEquals('getStatsAction',
            RequestInfoFactory::getHandlerActionForRequest(new Request('GET', '/_stats/')));
        $this->assertEquals('getStatsAction',
            RequestInfoFactory::getHandlerActionForRequest(new Request('GET', '/_stats/something')));

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('POST', '/_stats')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('POST', '/_stats/')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('POST', '/_stats/something')));

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('PUT', '/_stats')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('PUT', '/_stats/')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('PUT', '/_stats/something')));

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('DELETE', '/_stats')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('DELETE', '/_stats/')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('DELETE', '/_stats/something')));

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('HEAD', '/_stats')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('HEAD', '/_stats/')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('HEAD', '/_stats/something')));


        $this->assertEquals('getAllDbsAction',
            RequestInfoFactory::getHandlerActionForRequest(new Request('GET', '/_all_dbs')));
        $this->assertEquals('getAllDbsAction',
            RequestInfoFactory::getHandlerActionForRequest(new Request('GET', '/_all_dbs/')));

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('POST', '/_all_dbs')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('POST', '/_all_dbs/')));

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('PUT', '/_all_dbs')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('PUT', '/_all_dbs/')));

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('DELETE', '/_all_dbs')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('DELETE', '/_all_dbs/')));

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('HEAD', '/_all_dbs')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('HEAD', '/_all_dbs/')));


//		$this->assertEquals('postRestartAction', RequestInfoFactory::getHandlerActionForRequest(new Request('POST', '/_restart')));
//		$this->assertEquals('postRestartAction', RequestInfoFactory::getHandlerActionForRequest(new Request('POST', '/_restart/')));
//		$this->assertEquals('postRestartAction', RequestInfoFactory::getHandlerActionForRequest(new Request('POST', '/_restart/something')));

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('POST', '/_restart')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('POST', '/_restart/')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('POST', '/_restart/something')));

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('GET', '/_restart')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('GET', '/_restart/')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('GET', '/_restart/something')));

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('PUT', '/_restart')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('PUT', '/_restart/')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('PUT', '/_restart/something')));

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('DELETE', '/_restart')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('DELETE', '/_restart/')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('DELETE',
            '/_restart/something')));

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('HEAD', '/_restart')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('HEAD', '/_restart/')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('HEAD', '/_restart/something')));


        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('POST', '/restart')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('POST', '/restart/')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('POST', '/restart/something')));

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('GET', '/restart')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('GET', '/restart/')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('GET', '/restart/something')));

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('PUT', '/restart')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('PUT', '/restart/')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('PUT', '/restart/something')));

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('DELETE', '/restart')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('DELETE', '/restart/')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('DELETE', '/restart/something')));

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('HEAD', '/restart')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('HEAD', '/restart/')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('HEAD', '/restart/something')));


        $this->assertEquals('getCountAction',
            RequestInfoFactory::getHandlerActionForRequest(new Request('GET', '/_count')));
        $this->assertEquals('getCountAction',
            RequestInfoFactory::getHandlerActionForRequest(new Request('GET', '/_count/')));

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('POST', '/_count')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('POST', '/_count/')));

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('PUT', '/_count')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('PUT', '/_count/')));

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('DELETE', '/_count')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('DELETE', '/_count/')));

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('HEAD', '/_count')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('HEAD', '/_count/')));


        $this->assertEquals('getCountAction',
            RequestInfoFactory::getHandlerActionForRequest(new Request('GET', '/database-identifier/_count')));
        $this->assertEquals('getCountAction',
            RequestInfoFactory::getHandlerActionForRequest(new Request('GET', '/database-identifier/_count/')));

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('POST',
            '/database-identifier/_count')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('POST',
            '/database-identifier/_count/')));

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('PUT',
            '/database-identifier/_count')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('PUT',
            '/database-identifier/_count/')));

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('DELETE',
            '/database-identifier/_count')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('DELETE',
            '/database-identifier/_count/')));

        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('HEAD',
            '/database-identifier/_count')));
        $this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('HEAD',
            '/database-identifier/_count/')));

    }


    /**
     * @test
     */
    public function getHandlerForRequestTest()
    {
        class_alias('Cundd\\PersistentObjectStore\\Server\\ValueObject\\SpecialHandler',
            'Cundd\\PersistentObjectStore\\Server\\Handler\\SpecialHandler');
        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('GET', '/_restart')));
        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('GET', '/_restart/')));
        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('GET', '/_restart/something')));

        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('HEAD', '/_restart')));
        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('HEAD', '/_restart/')));
        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('HEAD', '/_restart/something')));

        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('PUT', '/_restart')));
        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('PUT', '/_restart/')));
        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('PUT', '/_restart/something')));

        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('DELETE', '/_restart')));
        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('DELETE', '/_restart/')));
        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('DELETE', '/_restart/something')));

        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('POST', '/_restart')));
        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('POST', '/_restart/')));
        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('POST', '/_restart/something')));


        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('GET', '/_shutdown')));
        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('GET', '/_shutdown/')));
        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('GET', '/_shutdown/something')));

        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('HEAD', '/_shutdown')));
        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('HEAD', '/_shutdown/')));
        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('HEAD', '/_shutdown/something')));

        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('PUT', '/_shutdown')));
        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('PUT', '/_shutdown/')));
        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('PUT', '/_shutdown/something')));

        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('DELETE', '/_shutdown')));
        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('DELETE', '/_shutdown/')));
        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('DELETE', '/_shutdown/something')));

        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('POST', '/_shutdown')));
        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('POST', '/_shutdown/')));
        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('POST', '/_shutdown/something')));


        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('GET', '/database-identifier')));
        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('GET', '/database-identifier/')));
        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('GET', '/database-identifier/something')));

        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('HEAD', '/database-identifier')));
        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('HEAD', '/database-identifier/')));
        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('HEAD', '/database-identifier/something')));

        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('PUT', '/database-identifier')));
        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('PUT', '/database-identifier/')));
        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('PUT', '/database-identifier/something')));

        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('DELETE', '/database-identifier')));
        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('DELETE', '/database-identifier/')));
        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('DELETE', '/database-identifier/something')));

        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('POST', '/database-identifier')));
        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('POST', '/database-identifier/')));
        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\HandlerInterface',
            RequestInfoFactory::getHandlerClassForRequest(new Request('POST', '/database-identifier/something')));


        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest(new Request('GET', '/_special')));
        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest(new Request('GET', '/_special/')));
        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest(new Request('GET', '/_special/something')));

        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest(new Request('HEAD', '/_special')));
        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest(new Request('HEAD', '/_special/')));
        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest(new Request('HEAD', '/_special/something')));

        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest(new Request('PUT', '/_special')));
        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest(new Request('PUT', '/_special/')));
        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest(new Request('PUT', '/_special/something')));

        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest(new Request('DELETE', '/_special')));
        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest(new Request('DELETE', '/_special/')));
        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest(new Request('DELETE', '/_special/something')));

        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest(new Request('POST', '/_special')));
        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest(new Request('POST', '/_special/')));
        $this->assertEquals('Cundd\\PersistentObjectStore\\Server\\Handler\\SpecialHandler',
            RequestInfoFactory::getHandlerClassForRequest(new Request('POST', '/_special/something')));


        $this->assertEquals('Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest(new Request('GET', '/_cundd_special')));
        $this->assertEquals('Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest(new Request('GET', '/_cundd_special/')));
        $this->assertEquals('Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest(new Request('GET', '/_cundd_special/something')));

        $this->assertEquals('Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest(new Request('HEAD', '/_cundd_special')));
        $this->assertEquals('Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest(new Request('HEAD', '/_cundd_special/')));
        $this->assertEquals('Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest(new Request('HEAD', '/_cundd_special/something')));

        $this->assertEquals('Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest(new Request('PUT', '/_cundd_special')));
        $this->assertEquals('Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest(new Request('PUT', '/_cundd_special/')));
        $this->assertEquals('Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest(new Request('PUT', '/_cundd_special/something')));

        $this->assertEquals('Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest(new Request('DELETE', '/_cundd_special')));
        $this->assertEquals('Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest(new Request('DELETE', '/_cundd_special/')));
        $this->assertEquals('Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest(new Request('DELETE', '/_cundd_special/something')));

        $this->assertEquals('Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest(new Request('POST', '/_cundd_special')));
        $this->assertEquals('Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest(new Request('POST', '/_cundd_special/')));
        $this->assertEquals('Cundd\\Special\\Application',
            RequestInfoFactory::getHandlerClassForRequest(new Request('POST', '/_cundd_special/something')));
    }
}
 