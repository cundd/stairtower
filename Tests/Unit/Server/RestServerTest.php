<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 01.11.14
 * Time: 10:45
 */

namespace Cundd\PersistentObjectStore\Server;

use Cundd\PersistentObjectStore\Configuration\ConfigurationManager;
use Cundd\PersistentObjectStore\Utility\DebugUtility;

/**
 * Test for REST commands
 *
 * @package Cundd\PersistentObjectStore\Server
 */
class RestServerTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @test
	 */
	public function fullServerTest() {
		$configurationManager = ConfigurationManager::getSharedInstance();
		$databaseIdentifier   = 'test-db-' . time();

		$document1HostName = 'database01.my-servers.local';
		$documentIdentifier1 = md5($document1HostName);

		$document2HostName = 'web01.my-servers.local';
		$documentIdentifier2 = md5($document2HostName);

		$expectedPath         = $configurationManager->getConfigurationForKeyPath('writeDataPath') . $databaseIdentifier . '.json';

		$testDocument1         = array(
			'id'   => $documentIdentifier1,
			'host' => $document1HostName,
			'name' => 'Database 01',
			'ip'   => '192.168.45.107',
			'os'   => 'CunddOS',
		);
		$testDocument2         = array(
			'id'   => $documentIdentifier2,
			'host' => $document2HostName,
			'name' => 'Database 02',
			'ip'   => '192.168.40.127',
			'os'   => 'CunddOS',
		);

		$this->_startServer($configurationManager);


		// Get the stats
		$response = $this->_performRestRequest('_stats');
		$this->assertArrayHasKey('version', $response);
		$this->assertArrayHasKey('guid', $response);


		// Create a database
		$response = $this->_performRestRequest($databaseIdentifier, 'PUT');
		$this->assertArrayHasKey('message', $response);
		$this->assertEquals(sprintf('Database "%s" created', $databaseIdentifier), $response['message']);
//		$this->assertFileExists($expectedPath);


		// List Documents in that database
		$response = $this->_performRestRequest($databaseIdentifier);
		$this->assertEmpty($response);


		// Add a Document
		$response = $this->_performRestRequest($databaseIdentifier, 'POST', $testDocument1);
		$this->assertEquals($testDocument1['id'], $response['id']);
		$this->assertEquals($testDocument1['name'], $response['name']);
		$this->assertEquals($testDocument1['ip'], $response['ip']);
		$this->assertEquals($testDocument1['os'], $response['os']);


		// List Documents in that database
		$response = $this->_performRestRequest($databaseIdentifier);
		$this->assertNotEmpty($response);
		$responseFirstDocument = $response[0];
		$this->assertEquals($testDocument1['id'], $responseFirstDocument['id']);
		$this->assertEquals($testDocument1['name'], $responseFirstDocument['name']);
		$this->assertEquals($testDocument1['ip'], $responseFirstDocument['ip']);
		$this->assertEquals($testDocument1['os'], $responseFirstDocument['os']);


		// Add another Document
		$response = $this->_performRestRequest($databaseIdentifier, 'POST', $testDocument2);
		$this->assertEquals($testDocument2['id'], $response['id']);
		$this->assertEquals($testDocument2['name'], $response['name']);
		$this->assertEquals($testDocument2['ip'], $response['ip']);
		$this->assertEquals($testDocument2['os'], $response['os']);


		// List Documents in that database
		$response = $this->_performRestRequest($databaseIdentifier);
		$this->assertNotEmpty($response);
		$this->assertEquals($testDocument1['id'], $response[0]['id']);
		$this->assertEquals($testDocument1['name'], $response[0]['name']);
		$this->assertEquals($testDocument1['ip'], $response[0]['ip']);
		$this->assertEquals($testDocument1['os'], $response[0]['os']);

		$this->assertEquals($testDocument2['id'], $response[1]['id']);
		$this->assertEquals($testDocument2['name'], $response[1]['name']);
		$this->assertEquals($testDocument2['ip'], $response[1]['ip']);
		$this->assertEquals($testDocument2['os'], $response[1]['os']);


		// Update a Document
		$testDocument1['os'] = 'Cundbuntu';
		$response = $this->_performRestRequest($databaseIdentifier . '/' . $documentIdentifier1, 'PUT', $testDocument1);
		$this->assertEquals($testDocument1['id'], $response['id']);
		$this->assertEquals($testDocument1['name'], $response['name']);
		$this->assertEquals($testDocument1['ip'], $response['ip']);
		$this->assertEquals($testDocument1['os'], $response['os']);


		// List Documents in that database
		$response = $this->_performRestRequest($databaseIdentifier);
		$this->assertNotEmpty($response);
		$responseFirstDocument = $response[0];
		$this->assertEquals($testDocument1['id'], $responseFirstDocument['id']);
		$this->assertEquals($testDocument1['name'], $responseFirstDocument['name']);
		$this->assertEquals($testDocument1['ip'], $responseFirstDocument['ip']);
		$this->assertEquals($testDocument1['os'], $responseFirstDocument['os']);


		// Find a Document
		$response = $this->_performRestRequest($databaseIdentifier . '/?os=' . $testDocument1['os']);
		$this->assertNotEmpty($response);
		$responseFirstDocument = $response[0];
		$this->assertEquals($testDocument1['id'], $responseFirstDocument['id']);
		$this->assertEquals($testDocument1['name'], $responseFirstDocument['name']);
		$this->assertEquals($testDocument1['ip'], $responseFirstDocument['ip']);
		$this->assertEquals($testDocument1['os'], $responseFirstDocument['os']);


		// Delete a Document
		$response = $this->_performRestRequest($databaseIdentifier . '/' . $documentIdentifier1, 'DELETE', $testDocument1);
		$this->assertArrayHasKey('message', $response);
		$this->assertEquals(sprintf('Document "%s" deleted', $documentIdentifier1), $response['message']);


		// List Documents in that database
		$response = $this->_performRestRequest($databaseIdentifier);
		$this->assertNotEmpty($response);
		$this->assertEquals($testDocument2['id'], $response[0]['id']);
		$this->assertEquals($testDocument2['name'], $response[0]['name']);
		$this->assertEquals($testDocument2['ip'], $response[0]['ip']);
		$this->assertEquals($testDocument2['os'], $response[0]['os']);


		// Delete a Document
		$response = $this->_performRestRequest($databaseIdentifier . '/' . $documentIdentifier2, 'DELETE', $testDocument1);
		$this->assertArrayHasKey('message', $response);
		$this->assertEquals(sprintf('Document "%s" deleted', $documentIdentifier2), $response['message']);


		// Delete a Document again should fail
		$response = $this->_performRestRequest($databaseIdentifier . '/' . $documentIdentifier2, 'DELETE', $testDocument1);
		$this->assertSame(FALSE, $response);


		// List Documents in that database
		$response = $this->_performRestRequest($databaseIdentifier);
		$this->assertEmpty($response);


		// Delete the database
		$response = $this->_performRestRequest($databaseIdentifier, 'DELETE');
		$this->assertArrayHasKey('message', $response);
		$this->assertEquals(sprintf('Database "%s" deleted', $databaseIdentifier), $response['message']);


		// The database should not exist anymore
		$response = $this->_performRestRequest($databaseIdentifier);
		$this->assertSame(FALSE, $response);


//		$this->assertFileExists($expectedPath);
//
//		$this->fixture->createDatabase($databaseIdentifier);
//
//		$this->assertFileExists($expectedPath);
//		unlink($expectedPath);
	}

	/**
	 * Performs a REST request
	 *
	 * @param string $request
	 * @param string $method
	 * @param array  $arguments
	 * @param bool   $jsonContent
	 * @return mixed|string
	 */
	protected function _performRestRequest($request, $method = 'GET', $arguments = array(), $jsonContent = FALSE) {
		$url = 'http://127.0.0.1:1338/' . $request;

		if ($jsonContent) {
			$content     = json_encode($jsonContent);
			$contentType = 'application/json';
		} else {
			$content     = http_build_query($arguments);
			$contentType = 'application/x-www-form-urlencoded';
		}

		$headers = array(
			'Content-Type: ' . $contentType,
			'Content-Length: ' . strlen($content),
		);

		$options  = array(
			'http' => array(
				'header'  => implode("\r\n", $headers),
				'method'  => $method,
				'content' => $content,
			),
		);
		$context  = stream_context_create($options);
		$response = @file_get_contents($url, FALSE, $context);
		if ($response) {
			return json_decode($response, TRUE);
		}
		return $response;
	}

	/**
	 * Start the server
	 */
	protected function _startServer() {
		// Start the server
		$serverBinPath = ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('basePath') . 'bin/server';
		$commandParts  = array(
			escapeshellcmd(sprintf('"%s"', $serverBinPath)), //
			'--test', // Run the server in test mode
			'> /dev/null &', // Run the server in the background
		);

//		printf('Run %s' . PHP_EOL, implode(' ', $commandParts));
		exec(implode(' ', $commandParts), $output, $returnValue);


		// Wait for the server to boot
		sleep(1);
	}
}
 