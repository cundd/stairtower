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
		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET', 'contacts/info@cundd.net'));
		$this->assertEquals('GET', $requestInfo->getMethod());
		$this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('info@cundd.net', $requestInfo->getDataIdentifier());
		$this->assertTrue($requestInfo->isReadRequest());
		$this->assertFalse($requestInfo->isWriteRequest());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('HEAD', 'contacts/info@cundd.net'));
		$this->assertEquals('HEAD', $requestInfo->getMethod());
		$this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('info@cundd.net', $requestInfo->getDataIdentifier());
		$this->assertTrue($requestInfo->isReadRequest());
		$this->assertFalse($requestInfo->isWriteRequest());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('POST', 'contacts/info@cundd.net'));
		$this->assertEquals('POST', $requestInfo->getMethod());
		$this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('info@cundd.net', $requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('PUT', 'contacts/info@cundd.net'));
		$this->assertEquals('PUT', $requestInfo->getMethod());
		$this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('info@cundd.net', $requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('DELETE', 'contacts/info@cundd.net'));
		$this->assertEquals('DELETE', $requestInfo->getMethod());
		$this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('info@cundd.net', $requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());


		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET', 'contacts/'));
		$this->assertEquals('GET', $requestInfo->getMethod());
		$this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
		$this->assertNull($requestInfo->getDataIdentifier());
		$this->assertTrue($requestInfo->isReadRequest());
		$this->assertFalse($requestInfo->isWriteRequest());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('HEAD', 'contacts/'));
		$this->assertEquals('HEAD', $requestInfo->getMethod());
		$this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
		$this->assertNull($requestInfo->getDataIdentifier());
		$this->assertTrue($requestInfo->isReadRequest());
		$this->assertFalse($requestInfo->isWriteRequest());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('POST', 'contacts/'));
		$this->assertEquals('POST', $requestInfo->getMethod());
		$this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
		$this->assertNull($requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('PUT', 'contacts/'));
		$this->assertEquals('PUT', $requestInfo->getMethod());
		$this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
		$this->assertNull($requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('DELETE', 'contacts/'));
		$this->assertEquals('DELETE', $requestInfo->getMethod());
		$this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
		$this->assertNull($requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());


		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET', 'contacts'));
		$this->assertEquals('GET', $requestInfo->getMethod());
		$this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
		$this->assertNull($requestInfo->getDataIdentifier());
		$this->assertTrue($requestInfo->isReadRequest());
		$this->assertFalse($requestInfo->isWriteRequest());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('HEAD', 'contacts'));
		$this->assertEquals('HEAD', $requestInfo->getMethod());
		$this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
		$this->assertNull($requestInfo->getDataIdentifier());
		$this->assertTrue($requestInfo->isReadRequest());
		$this->assertFalse($requestInfo->isWriteRequest());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('POST', 'contacts'));
		$this->assertEquals('POST', $requestInfo->getMethod());
		$this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
		$this->assertNull($requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('PUT', 'contacts'));
		$this->assertEquals('PUT', $requestInfo->getMethod());
		$this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
		$this->assertNull($requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('DELETE', 'contacts'));
		$this->assertEquals('DELETE', $requestInfo->getMethod());
		$this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
		$this->assertNull($requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());


		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET', 'contacts/my-info-path'));
		$this->assertEquals('GET', $requestInfo->getMethod());
		$this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
		$this->assertTrue($requestInfo->isReadRequest());
		$this->assertFalse($requestInfo->isWriteRequest());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('HEAD', 'contacts/my-info-path'));
		$this->assertEquals('HEAD', $requestInfo->getMethod());
		$this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
		$this->assertTrue($requestInfo->isReadRequest());
		$this->assertFalse($requestInfo->isWriteRequest());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('POST', 'contacts/my-info-path'));
		$this->assertEquals('POST', $requestInfo->getMethod());
		$this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('PUT', 'contacts/my-info-path'));
		$this->assertEquals('PUT', $requestInfo->getMethod());
		$this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('DELETE', 'contacts/my-info-path'));
		$this->assertEquals('DELETE', $requestInfo->getMethod());
		$this->assertEquals('contacts', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());


		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET', 'contacts-database/my-info-path'));
		$this->assertEquals('GET', $requestInfo->getMethod());
		$this->assertEquals('contacts-database', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
		$this->assertTrue($requestInfo->isReadRequest());
		$this->assertFalse($requestInfo->isWriteRequest());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('HEAD', 'contacts-database/my-info-path'));
		$this->assertEquals('HEAD', $requestInfo->getMethod());
		$this->assertEquals('contacts-database', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
		$this->assertTrue($requestInfo->isReadRequest());
		$this->assertFalse($requestInfo->isWriteRequest());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('POST', 'contacts-database/my-info-path'));
		$this->assertEquals('POST', $requestInfo->getMethod());
		$this->assertEquals('contacts-database', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('PUT', 'contacts-database/my-info-path'));
		$this->assertEquals('PUT', $requestInfo->getMethod());
		$this->assertEquals('contacts-database', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('DELETE', 'contacts-database/my-info-path'));
		$this->assertEquals('DELETE', $requestInfo->getMethod());
		$this->assertEquals('contacts-database', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my-info-path', $requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());



		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET', 'contacts_database/my_info-path'));
		$this->assertEquals('GET', $requestInfo->getMethod());
		$this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my_info-path', $requestInfo->getDataIdentifier());
		$this->assertTrue($requestInfo->isReadRequest());
		$this->assertFalse($requestInfo->isWriteRequest());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('HEAD', 'contacts_database/my_info-path'));
		$this->assertEquals('HEAD', $requestInfo->getMethod());
		$this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my_info-path', $requestInfo->getDataIdentifier());
		$this->assertTrue($requestInfo->isReadRequest());
		$this->assertFalse($requestInfo->isWriteRequest());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('POST', 'contacts_database/my_info-path'));
		$this->assertEquals('POST', $requestInfo->getMethod());
		$this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my_info-path', $requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('PUT', 'contacts_database/my_info-path'));
		$this->assertEquals('PUT', $requestInfo->getMethod());
		$this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my_info-path', $requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('DELETE', 'contacts_database/my_info-path'));
		$this->assertEquals('DELETE', $requestInfo->getMethod());
		$this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my_info-path', $requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());



		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET', 'contacts_database/my-super_email@a-smthng.com'));
		$this->assertEquals('GET', $requestInfo->getMethod());
		$this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
		$this->assertTrue($requestInfo->isReadRequest());
		$this->assertFalse($requestInfo->isWriteRequest());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('HEAD', 'contacts_database/my-super_email@a-smthng.com'));
		$this->assertEquals('HEAD', $requestInfo->getMethod());
		$this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
		$this->assertTrue($requestInfo->isReadRequest());
		$this->assertFalse($requestInfo->isWriteRequest());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('POST', 'contacts_database/my-super_email@a-smthng.com'));
		$this->assertEquals('POST', $requestInfo->getMethod());
		$this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('PUT', 'contacts_database/my-super_email@a-smthng.com'));
		$this->assertEquals('PUT', $requestInfo->getMethod());
		$this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('DELETE', 'contacts_database/my-super_email@a-smthng.com'));
		$this->assertEquals('DELETE', $requestInfo->getMethod());
		$this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());



		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('GET', 'contacts_database/my-super_email@a-smthng.com/something-more'));
		$this->assertEquals('GET', $requestInfo->getMethod());
		$this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
		$this->assertTrue($requestInfo->isReadRequest());
		$this->assertFalse($requestInfo->isWriteRequest());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('HEAD', 'contacts_database/my-super_email@a-smthng.com/something-more'));
		$this->assertEquals('HEAD', $requestInfo->getMethod());
		$this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
		$this->assertTrue($requestInfo->isReadRequest());
		$this->assertFalse($requestInfo->isWriteRequest());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('POST', 'contacts_database/my-super_email@a-smthng.com/something-more'));
		$this->assertEquals('POST', $requestInfo->getMethod());
		$this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('PUT', 'contacts_database/my-super_email@a-smthng.com/something-more'));
		$this->assertEquals('PUT', $requestInfo->getMethod());
		$this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());

		$requestInfo = RequestInfoFactory::buildRequestInfoFromRequest(new Request('DELETE', 'contacts_database/my-super_email@a-smthng.com/something-more'));
		$this->assertEquals('DELETE', $requestInfo->getMethod());
		$this->assertEquals('contacts_database', $requestInfo->getDatabaseIdentifier());
		$this->assertEquals('my-super_email@a-smthng.com', $requestInfo->getDataIdentifier());
		$this->assertFalse($requestInfo->isReadRequest());
		$this->assertTrue($requestInfo->isWriteRequest());






	}
}
 