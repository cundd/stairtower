<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 01.11.14
 * Time: 10:45
 */

namespace Cundd\PersistentObjectStore\Server;

use Cundd\PersistentObjectStore\Configuration\ConfigurationManager;
use Cundd\PersistentObjectStore\Constants;
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
		$databaseIdentifier = 'test-db-' . time();

		$document1HostName   = 'database01.my-servers.local';
		$documentIdentifier1 = md5($document1HostName);

		$document2HostName   = 'web01.my-servers.local';
		$documentIdentifier2 = md5($document2HostName);

//		$expectedPath         = ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('writeDataPath') . $databaseIdentifier . '.json';

		$testDocument1 = array(
			Constants::DATA_ID_KEY => $documentIdentifier1,
			'host'                 => $document1HostName,
			'name'                 => 'Database 01',
			'ip'                   => '192.168.45.107',
			'os'                   => 'CunddOS',
		);
		$testDocument2 = array(
			Constants::DATA_ID_KEY => $documentIdentifier2,
			'host'                 => $document2HostName,
			'name'                 => 'Database 02',
			'ip'                   => '192.168.40.127',
			'os'                   => 'CunddOS',
		);

		$this->_startServer();


		// Get the welcome message
		$response = $this->_performRestRequest('');
		$this->assertArrayHasKey('message', $response);
		$this->assertEquals(Constants::MESSAGE_JSON_WELCOME, $response['message']);


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
		$this->assertEquals($testDocument1[Constants::DATA_ID_KEY], $response[Constants::DATA_ID_KEY]);
		$this->assertEquals($testDocument1['name'], $response['name']);
		$this->assertEquals($testDocument1['ip'], $response['ip']);
		$this->assertEquals($testDocument1['os'], $response['os']);


		// List Documents in that database
		$response = $this->_performRestRequest($databaseIdentifier);
		$this->assertNotEmpty($response);
		$responseFirstDocument = $response[0];
		$this->assertEquals($testDocument1[Constants::DATA_ID_KEY], $responseFirstDocument[Constants::DATA_ID_KEY]);
		$this->assertEquals($testDocument1['name'], $responseFirstDocument['name']);
		$this->assertEquals($testDocument1['ip'], $responseFirstDocument['ip']);
		$this->assertEquals($testDocument1['os'], $responseFirstDocument['os']);


		// Add another Document
		$response = $this->_performRestRequest($databaseIdentifier, 'POST', $testDocument2);
		$this->assertEquals($testDocument2[Constants::DATA_ID_KEY], $response[Constants::DATA_ID_KEY]);
		$this->assertEquals($testDocument2['name'], $response['name']);
		$this->assertEquals($testDocument2['ip'], $response['ip']);
		$this->assertEquals($testDocument2['os'], $response['os']);


		// List Documents in that database
		$response = $this->_performRestRequest($databaseIdentifier);
		$this->assertNotEmpty($response);
		$this->assertEquals($testDocument1[Constants::DATA_ID_KEY], $response[0][Constants::DATA_ID_KEY]);
		$this->assertEquals($testDocument1['name'], $response[0]['name']);
		$this->assertEquals($testDocument1['ip'], $response[0]['ip']);
		$this->assertEquals($testDocument1['os'], $response[0]['os']);

		$this->assertEquals($testDocument2[Constants::DATA_ID_KEY], $response[1][Constants::DATA_ID_KEY]);
		$this->assertEquals($testDocument2['name'], $response[1]['name']);
		$this->assertEquals($testDocument2['ip'], $response[1]['ip']);
		$this->assertEquals($testDocument2['os'], $response[1]['os']);


		// Update a Document
		$testDocument1['os'] = 'Cundbuntu';
		$response            = $this->_performRestRequest($databaseIdentifier . '/' . $documentIdentifier1, 'PUT', $testDocument1);
		$this->assertEquals($testDocument1[Constants::DATA_ID_KEY], $response[Constants::DATA_ID_KEY]);
		$this->assertEquals($testDocument1['name'], $response['name']);
		$this->assertEquals($testDocument1['ip'], $response['ip']);
		$this->assertEquals($testDocument1['os'], $response['os']);


		// List Documents in that database
		$response = $this->_performRestRequest($databaseIdentifier);
		$this->assertNotEmpty($response);
		$responseFirstDocument = $response[0];
		$this->assertEquals($testDocument1[Constants::DATA_ID_KEY], $responseFirstDocument[Constants::DATA_ID_KEY]);
		$this->assertEquals($testDocument1['name'], $responseFirstDocument['name']);
		$this->assertEquals($testDocument1['ip'], $responseFirstDocument['ip']);
		$this->assertEquals($testDocument1['os'], $responseFirstDocument['os']);


		// Find a Document
		$response = $this->_performRestRequest($databaseIdentifier . '/?os=' . $testDocument1['os']);
		$this->assertNotEmpty($response);
		$responseFirstDocument = $response[0];
		$this->assertEquals($testDocument1[Constants::DATA_ID_KEY], $responseFirstDocument[Constants::DATA_ID_KEY]);
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
		$this->assertEquals($testDocument2[Constants::DATA_ID_KEY], $response[0][Constants::DATA_ID_KEY]);
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


		// Shutdown the server
		$response = $this->_performRestRequest('_shutdown', 'POST');
		$this->assertArrayHasKey('message', $response);
		$this->assertEquals('Server is going to shut down', $response['message']);
		sleep(1);


		// The server should not send the welcome message
		$response = $this->_performRestRequest('');
		$this->assertSame(FALSE, $response);


//		$this->assertFileExists($expectedPath);
//		unlink($expectedPath);
	}

	/**
	 * @test
	 */
	public function letItRockTest() {
		$databaseIdentifier = 'test-db-' . time();


		$this->_startServer(20);

		// Create a database
		$response = $this->_performRestRequest($databaseIdentifier, 'PUT');
		$this->assertArrayHasKey('message', $response);
		$this->assertEquals(sprintf('Database "%s" created', $databaseIdentifier), $response['message']);
//		$this->assertFileExists($expectedPath);


		// Create Documents
		$i = 0;

		// TODO: 1000 is very low speed up the Database::add() method
		while (++$i < 1000) {
			$documentHostName   = 'database' . $i . '.my-servers.local';
			$documentIdentifier = md5($documentHostName);

			$testDocument = array(
				'id'   => $documentIdentifier,
				'host' => $documentHostName,
				'name' => 'Database 0' . $i,
				'ip'   => '192.168.45.107',
				'os'   => 'CunddOS',
			);

			$response = $this->_performRestRequest($databaseIdentifier, 'POST', $testDocument);
			$this->assertEquals($testDocument['name'], $response['name']);
			$this->assertEquals($testDocument['id'], $response['id']);
			$this->assertEquals($testDocument['ip'], $response['ip']);
			$this->assertEquals($testDocument['os'], $response['os']);
		}

		// List Documents in that database
		$response = $this->_performRestRequest($databaseIdentifier);
		$this->assertNotEmpty($response);
//		$responseFirstDocument = $response[0];
//		$this->assertEquals($testDocument['id'], $responseFirstDocument['id']);
//		$this->assertEquals($testDocument['name'], $responseFirstDocument['name']);
//		$this->assertEquals($testDocument['ip'], $responseFirstDocument['ip']);
//		$this->assertEquals($testDocument['os'], $responseFirstDocument['os']);


//		// Delete the database
//		$response = $this->_performRestRequest($databaseIdentifier, 'DELETE');
//		$this->assertArrayHasKey('message', $response);
//		$this->assertEquals(sprintf('Database "%s" deleted', $databaseIdentifier), $response['message']);

		// Shutdown the server
		$response = $this->_performRestRequest('_shutdown', 'POST');
		$this->assertArrayHasKey('message', $response);
		$this->assertEquals('Server is going to shut down', $response['message']);
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
	 *
	 * @param int $autoShutdownTime
	 */
	protected function _startServer($autoShutdownTime = 7) {
		// Start the server
		$serverBinPath = ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('basePath') . 'bin/server';
		$commandParts  = array(
			escapeshellcmd(sprintf('"%s"', $serverBinPath)), //
		);
		if ($autoShutdownTime > -1) {
			$commandParts[] = '--test=' . $autoShutdownTime; // Run the server in test mode
		}
		$commandParts[] = '> /dev/null &'; // Run the server in the background


//		printf('Run %s' . PHP_EOL, implode(' ', $commandParts));
		exec(implode(' ', $commandParts), $output, $returnValue);


		// Wait for the server to boot
		sleep(1);
	}
}
 