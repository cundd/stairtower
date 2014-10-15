<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 11.10.14
 * Time: 20:20
 */

namespace Cundd\PersistentObjectStore\Server\ValueObject;

use React\Http\Request;

/**
 * Tests for creating RequestInfo objects
 *
 * @package Cundd\PersistentObjectStore\Server\ValueObject
 */
class RequestInfoTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @test
	 */
	public function differentTests() {
		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET', '/contacts/info@cundd.net'));
		$this->assertEquals('GET', $requestInfo->getMethod());
		$this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('info@cundd.net', $requestInfo->getDataIdentifier());
		$this->assertTrue($requestInfo->isReadRequest());
		$this->assertFalse($requestInfo->isWriteRequest());
		$this->assertNull($requestInfo->getSpecialHandlerAction());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('HEAD', '/contacts/info@cundd.net'));
		$this->assertEquals('HEAD', $requestInfo->getMethod());
		$this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('info@cundd.net', $requestInfo->getDataIdentifier());
		$this->assertTrue($requestInfo->isReadRequest());
		$this->assertFalse($requestInfo->isWriteRequest());
		$this->assertNull($requestInfo->getSpecialHandlerAction());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('POST', '/contacts/info@cundd.net'));
		$this->assertEquals('POST', $requestInfo->getMethod());
		$this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('info@cundd.net', $requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());
		$this->assertNull($requestInfo->getSpecialHandlerAction());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('PUT', '/contacts/info@cundd.net'));
		$this->assertEquals('PUT', $requestInfo->getMethod());
		$this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('info@cundd.net', $requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());
		$this->assertNull($requestInfo->getSpecialHandlerAction());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('DELETE', '/contacts/info@cundd.net'));
		$this->assertEquals('DELETE', $requestInfo->getMethod());
		$this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('info@cundd.net', $requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());
		$this->assertNull($requestInfo->getSpecialHandlerAction());


		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET', '/contacts/'));
		$this->assertEquals('GET', $requestInfo->getMethod());
		$this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
		$this->assertNull($requestInfo->getDataIdentifier());
		$this->assertTrue($requestInfo->isReadRequest());
		$this->assertFalse($requestInfo->isWriteRequest());
		$this->assertNull($requestInfo->getSpecialHandlerAction());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('HEAD', '/contacts/'));
		$this->assertEquals('HEAD', $requestInfo->getMethod());
		$this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
		$this->assertNull($requestInfo->getDataIdentifier());
		$this->assertTrue($requestInfo->isReadRequest());
		$this->assertFalse($requestInfo->isWriteRequest());
		$this->assertNull($requestInfo->getSpecialHandlerAction());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('POST', '/contacts/'));
		$this->assertEquals('POST', $requestInfo->getMethod());
		$this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
		$this->assertNull($requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());
		$this->assertNull($requestInfo->getSpecialHandlerAction());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('PUT', '/contacts/'));
		$this->assertEquals('PUT', $requestInfo->getMethod());
		$this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
		$this->assertNull($requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());
		$this->assertNull($requestInfo->getSpecialHandlerAction());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('DELETE', '/contacts/'));
		$this->assertEquals('DELETE', $requestInfo->getMethod());
		$this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
		$this->assertNull($requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());
		$this->assertNull($requestInfo->getSpecialHandlerAction());


		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET', '/contacts'));
		$this->assertEquals('GET', $requestInfo->getMethod());
		$this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
		$this->assertNull($requestInfo->getDataIdentifier());
		$this->assertTrue($requestInfo->isReadRequest());
		$this->assertFalse($requestInfo->isWriteRequest());
		$this->assertNull($requestInfo->getSpecialHandlerAction());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('HEAD', '/contacts'));
		$this->assertEquals('HEAD', $requestInfo->getMethod());
		$this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
		$this->assertNull($requestInfo->getDataIdentifier());
		$this->assertTrue($requestInfo->isReadRequest());
		$this->assertFalse($requestInfo->isWriteRequest());
		$this->assertNull($requestInfo->getSpecialHandlerAction());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('POST', '/contacts'));
		$this->assertEquals('POST', $requestInfo->getMethod());
		$this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
		$this->assertNull($requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());
		$this->assertNull($requestInfo->getSpecialHandlerAction());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('PUT', '/contacts'));
		$this->assertEquals('PUT', $requestInfo->getMethod());
		$this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
		$this->assertNull($requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());
		$this->assertNull($requestInfo->getSpecialHandlerAction());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('DELETE', '/contacts'));
		$this->assertEquals('DELETE', $requestInfo->getMethod());
		$this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
		$this->assertNull($requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());
		$this->assertNull($requestInfo->getSpecialHandlerAction());


		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET', '/contacts/my-info-path'));
		$this->assertEquals('GET', $requestInfo->getMethod());
		$this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
		$this->assertTrue($requestInfo->isReadRequest());
		$this->assertFalse($requestInfo->isWriteRequest());
		$this->assertNull($requestInfo->getSpecialHandlerAction());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('HEAD', '/contacts/my-info-path'));
		$this->assertEquals('HEAD', $requestInfo->getMethod());
		$this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
		$this->assertTrue($requestInfo->isReadRequest());
		$this->assertFalse($requestInfo->isWriteRequest());
		$this->assertNull($requestInfo->getSpecialHandlerAction());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('POST', '/contacts/my-info-path'));
		$this->assertEquals('POST', $requestInfo->getMethod());
		$this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());
		$this->assertNull($requestInfo->getSpecialHandlerAction());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('PUT', '/contacts/my-info-path'));
		$this->assertEquals('PUT', $requestInfo->getMethod());
		$this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());
		$this->assertNull($requestInfo->getSpecialHandlerAction());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('DELETE', '/contacts/my-info-path'));
		$this->assertEquals('DELETE', $requestInfo->getMethod());
		$this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());
		$this->assertNull($requestInfo->getSpecialHandlerAction());


		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET', '/contacts-database/my-info-path'));
		$this->assertEquals('GET', $requestInfo->getMethod());
		$this->assertEquals('contacts-database', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
		$this->assertTrue($requestInfo->isReadRequest());
		$this->assertFalse($requestInfo->isWriteRequest());
		$this->assertNull($requestInfo->getSpecialHandlerAction());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('HEAD', '/contacts-database/my-info-path'));
		$this->assertEquals('HEAD', $requestInfo->getMethod());
		$this->assertEquals('contacts-database', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
		$this->assertTrue($requestInfo->isReadRequest());
		$this->assertFalse($requestInfo->isWriteRequest());
		$this->assertNull($requestInfo->getSpecialHandlerAction());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('POST', '/contacts-database/my-info-path'));
		$this->assertEquals('POST', $requestInfo->getMethod());
		$this->assertEquals('contacts-database', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());
		$this->assertNull($requestInfo->getSpecialHandlerAction());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('PUT', '/contacts-database/my-info-path'));
		$this->assertEquals('PUT', $requestInfo->getMethod());
		$this->assertEquals('contacts-database', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());
		$this->assertNull($requestInfo->getSpecialHandlerAction());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('DELETE', '/contacts-database/my-info-path'));
		$this->assertEquals('DELETE', $requestInfo->getMethod());
		$this->assertEquals('contacts-database', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());
		$this->assertNull($requestInfo->getSpecialHandlerAction());


		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET', '/contacts_database/my_info-path'));
		$this->assertEquals('GET', $requestInfo->getMethod());
		$this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my_info-path', $requestInfo->getDataIdentifier());
		$this->assertTrue($requestInfo->isReadRequest());
		$this->assertFalse($requestInfo->isWriteRequest());
		$this->assertNull($requestInfo->getSpecialHandlerAction());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('HEAD', '/contacts_database/my_info-path'));
		$this->assertEquals('HEAD', $requestInfo->getMethod());
		$this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my_info-path', $requestInfo->getDataIdentifier());
		$this->assertTrue($requestInfo->isReadRequest());
		$this->assertFalse($requestInfo->isWriteRequest());
		$this->assertNull($requestInfo->getSpecialHandlerAction());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('POST', '/contacts_database/my_info-path'));
		$this->assertEquals('POST', $requestInfo->getMethod());
		$this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my_info-path', $requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());
		$this->assertNull($requestInfo->getSpecialHandlerAction());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('PUT', '/contacts_database/my_info-path'));
		$this->assertEquals('PUT', $requestInfo->getMethod());
		$this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my_info-path', $requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());
		$this->assertNull($requestInfo->getSpecialHandlerAction());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('DELETE', '/contacts_database/my_info-path'));
		$this->assertEquals('DELETE', $requestInfo->getMethod());
		$this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my_info-path', $requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());
		$this->assertNull($requestInfo->getSpecialHandlerAction());


		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET', '/contacts_database/my-super_email@a-smthng.com'));
		$this->assertEquals('GET', $requestInfo->getMethod());
		$this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
		$this->assertTrue($requestInfo->isReadRequest());
		$this->assertFalse($requestInfo->isWriteRequest());
		$this->assertNull($requestInfo->getSpecialHandlerAction());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('HEAD', '/contacts_database/my-super_email@a-smthng.com'));
		$this->assertEquals('HEAD', $requestInfo->getMethod());
		$this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
		$this->assertTrue($requestInfo->isReadRequest());
		$this->assertFalse($requestInfo->isWriteRequest());
		$this->assertNull($requestInfo->getSpecialHandlerAction());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('POST', '/contacts_database/my-super_email@a-smthng.com'));
		$this->assertEquals('POST', $requestInfo->getMethod());
		$this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());
		$this->assertNull($requestInfo->getSpecialHandlerAction());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('PUT', '/contacts_database/my-super_email@a-smthng.com'));
		$this->assertEquals('PUT', $requestInfo->getMethod());
		$this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());
		$this->assertNull($requestInfo->getSpecialHandlerAction());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('DELETE', '/contacts_database/my-super_email@a-smthng.com'));
		$this->assertEquals('DELETE', $requestInfo->getMethod());
		$this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());
		$this->assertNull($requestInfo->getSpecialHandlerAction());


		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET', '/contacts_database/my-super_email@a-smthng.com/something-more'));
		$this->assertEquals('GET', $requestInfo->getMethod());
		$this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
		$this->assertTrue($requestInfo->isReadRequest());
		$this->assertFalse($requestInfo->isWriteRequest());
		$this->assertNull($requestInfo->getSpecialHandlerAction());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('HEAD', '/contacts_database/my-super_email@a-smthng.com/something-more'));
		$this->assertEquals('HEAD', $requestInfo->getMethod());
		$this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
		$this->assertTrue($requestInfo->isReadRequest());
		$this->assertFalse($requestInfo->isWriteRequest());
		$this->assertNull($requestInfo->getSpecialHandlerAction());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('POST', '/contacts_database/my-super_email@a-smthng.com/something-more'));
		$this->assertEquals('POST', $requestInfo->getMethod());
		$this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());
		$this->assertNull($requestInfo->getSpecialHandlerAction());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('PUT', '/contacts_database/my-super_email@a-smthng.com/something-more'));
		$this->assertEquals('PUT', $requestInfo->getMethod());
		$this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());
		$this->assertNull($requestInfo->getSpecialHandlerAction());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('DELETE', '/contacts_database/my-super_email@a-smthng.com/something-more'));
		$this->assertEquals('DELETE', $requestInfo->getMethod());
		$this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());
		$this->assertNull($requestInfo->getSpecialHandlerAction());






	}

	/**
	 * @test
	 */
	public function getServerActionForRequestTest() {
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
		$this->assertEquals('restart', RequestInfoFactory::getServerActionForRequest(new Request('POST', '/_restart/')));
		$this->assertEquals('restart', RequestInfoFactory::getServerActionForRequest(new Request('POST', '/_restart/something')));


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
		$this->assertFalse(RequestInfoFactory::getServerActionForRequest(new Request('DELETE', '/_shutdown/something')));

		$this->assertEquals('shutdown', RequestInfoFactory::getServerActionForRequest(new Request('POST', '/_shutdown')));
		$this->assertEquals('shutdown', RequestInfoFactory::getServerActionForRequest(new Request('POST', '/_shutdown/')));
		$this->assertEquals('shutdown', RequestInfoFactory::getServerActionForRequest(new Request('POST', '/_shutdown/something')));


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
	public function getHandlerActionForRequestTest() {
		$this->assertEquals('getStatsAction', RequestInfoFactory::getHandlerActionForRequest(new Request('GET', '/_stats')));
		$this->assertEquals('getStatsAction', RequestInfoFactory::getHandlerActionForRequest(new Request('GET', '/_stats/')));
		$this->assertEquals('getStatsAction', RequestInfoFactory::getHandlerActionForRequest(new Request('GET', '/_stats/something')));

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




		$this->assertEquals('getAllDbsAction', RequestInfoFactory::getHandlerActionForRequest(new Request('GET', '/_all_dbs')));
		$this->assertEquals('getAllDbsAction', RequestInfoFactory::getHandlerActionForRequest(new Request('GET', '/_all_dbs/')));

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
		$this->assertFalse(RequestInfoFactory::getHandlerActionForRequest(new Request('DELETE', '/_restart/something')));

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
	}
}
 