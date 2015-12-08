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

/**
 * Test for REST commands
 *
 * @package Cundd\PersistentObjectStore\Server
 */
class RestServerTest extends \PHPUnit_Framework_TestCase
{
    protected $databaseIdentifier;
    protected $expectedPath;
    protected $portForTestServer = 7700;

    protected function setUp()
    {
        parent::setUp();

        $this->databaseIdentifier = $databaseIdentifier = 'test-db-'.time();
        $this->expectedPath       = ConfigurationManager::getSharedInstance()
                ->getConfigurationForKeyPath('writeDataPath')
            .$databaseIdentifier.'.json';
        $this->expectedPath       = __DIR__.'/../../var/Data/'.$databaseIdentifier.'.json';
    }

    protected function tearDown()
    {
        if (file_exists($this->expectedPath)) {
            unlink($this->expectedPath);
        }

        $writeDataPath = ConfigurationManager::getSharedInstance()
                ->getConfigurationForKeyPath('writeDataPath')
            .$this->databaseIdentifier.'.json';
        if (file_exists($writeDataPath)) {
            unlink($writeDataPath);
        }
        parent::tearDown();
    }


    /**
     * @test
     */
    public function fullServerTest()
    {
        // $start = microtime(1);

        $databaseIdentifier = $this->databaseIdentifier;

        $document1HostName   = 'database01.my-servers.local';
        $documentIdentifier1 = md5($document1HostName);

        $document2HostName   = 'web01.my-servers.local';
        $documentIdentifier2 = md5($document2HostName);

        $expectedPath = __DIR__.'/../../var/Data/'.$databaseIdentifier.'.json';

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

        $this->startServer();


        // Get the welcome message
        $response = $this->performRestRequest('');
        $this->assertNotEquals(false, $response, 'Could not get the welcome message');
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals(Constants::MESSAGE_JSON_WELCOME, $response['message']);


        // Get the stats
        $response = $this->performRestRequest('_stats');
        $this->assertArrayHasKey('version', $response, 'Could not get the stats');
        $this->assertArrayHasKey('guid', $response);


        // Create a database
        $response = $this->performRestRequest($databaseIdentifier, 'PUT');
        $this->assertNotEquals(false, $response, 'Could not create the Database');
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals(sprintf('Database "%s" created', $databaseIdentifier), $response['message']);


        // List all Databases
        $response = $this->performRestRequest('_all_dbs');
        $this->assertNotEquals(false, $response, 'Could not list all Databases');
        $this->assertTrue(in_array($this->databaseIdentifier, $response));


        // List Documents in that database
        $response = $this->performRestRequest($databaseIdentifier);
        $this->assertEmpty($response, 'Database should be empty');


        // Add a Document
        $response = $this->performRestRequest($databaseIdentifier, 'POST', $testDocument1);
        $this->assertEquals(
            $testDocument1[Constants::DATA_ID_KEY],
            $response[Constants::DATA_ID_KEY],
            'Could not add the Document'
        );
        $this->assertEquals($testDocument1['name'], $response['name']);
        $this->assertEquals($testDocument1['ip'], $response['ip']);
        $this->assertEquals($testDocument1['os'], $response['os']);


        // List Documents in that database
        $response = $this->performRestRequest($databaseIdentifier);
        $this->assertNotEmpty($response, 'Could not list the Documents');
        $responseFirstDocument = $response[0];
        $this->assertEquals($testDocument1[Constants::DATA_ID_KEY], $responseFirstDocument[Constants::DATA_ID_KEY]);
        $this->assertEquals($testDocument1['name'], $responseFirstDocument['name']);
        $this->assertEquals($testDocument1['ip'], $responseFirstDocument['ip']);
        $this->assertEquals($testDocument1['os'], $responseFirstDocument['os']);


        // Add another Document
        $response = $this->performRestRequest($databaseIdentifier, 'POST', $testDocument2);
        $this->assertNotSame(false, $response, 'Could not add another Document');
        $this->assertEquals($testDocument2[Constants::DATA_ID_KEY], $response[Constants::DATA_ID_KEY]);
        $this->assertEquals($testDocument2['name'], $response['name'], 'Could not add another Document');
        $this->assertEquals($testDocument2['ip'], $response['ip']);
        $this->assertEquals($testDocument2['os'], $response['os']);


        // List Documents in that database
        $response = $this->performRestRequest($databaseIdentifier);
        $this->assertNotEmpty($response, 'Database does not contain any Documents');
        $this->assertEquals($testDocument1[Constants::DATA_ID_KEY], $response[0][Constants::DATA_ID_KEY]);
        $this->assertEquals($testDocument1['name'], $response[0]['name']);
        $this->assertEquals($testDocument1['ip'], $response[0]['ip']);
        $this->assertEquals($testDocument1['os'], $response[0]['os']);

        $this->assertEquals($testDocument2[Constants::DATA_ID_KEY], $response[1][Constants::DATA_ID_KEY]);
        $this->assertEquals($testDocument2['name'], $response[1]['name']);
        $this->assertEquals($testDocument2['ip'], $response[1]['ip']);
        $this->assertEquals($testDocument2['os'], $response[1]['os']);


        // Count Documents in that database
        $response = $this->performRestRequest($databaseIdentifier.'/_count');
        $this->assertNotEmpty($response, 'Could not count the Documents in the Database');
        $this->assertEquals(2, $response['count']);


        // Update a Document
        $testDocument1['os'] = 'Cundbuntu';
        $response            = $this->performRestRequest(
            $databaseIdentifier.'/'.$documentIdentifier1,
            'PUT',
            $testDocument1
        );
        $this->assertEquals(
            $testDocument1[Constants::DATA_ID_KEY],
            $response[Constants::DATA_ID_KEY],
            'Could not update the Document'
        );
        $this->assertEquals($testDocument1['name'], $response['name']);
        $this->assertEquals($testDocument1['ip'], $response['ip']);
        $this->assertEquals($testDocument1['os'], $response['os']);


        // List Documents in that database
        $response = $this->performRestRequest($databaseIdentifier);
        $this->assertNotEmpty($response);
        $responseFirstDocument = $response[0];
        $this->assertEquals(
            $testDocument1[Constants::DATA_ID_KEY],
            $responseFirstDocument[Constants::DATA_ID_KEY],
            'Could not get the stats'
        );
        $this->assertEquals($testDocument1['name'], $responseFirstDocument['name']);
        $this->assertEquals($testDocument1['ip'], $responseFirstDocument['ip']);
        $this->assertEquals($testDocument1['os'], $responseFirstDocument['os']);


        // Find a Document
        $response = $this->performRestRequest($databaseIdentifier.'/?os='.$testDocument1['os']);
        $this->assertNotEmpty($response);
        $responseFirstDocument = $response[0];
        $this->assertEquals(
            $testDocument1[Constants::DATA_ID_KEY],
            $responseFirstDocument[Constants::DATA_ID_KEY],
            'Did not find a Document'
        );
        $this->assertEquals($testDocument1['name'], $responseFirstDocument['name']);
        $this->assertEquals($testDocument1['ip'], $responseFirstDocument['ip']);
        $this->assertEquals($testDocument1['os'], $responseFirstDocument['os']);


        // Delete a Document
        $response = $this->performRestRequest(
            $databaseIdentifier.'/'.$documentIdentifier1,
            'DELETE',
            $testDocument1
        );
        $this->assertNotEquals(false, $response, 'Could not delete the Document');
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals(sprintf('Document "%s" deleted', $documentIdentifier1), $response['message']);


        // List Documents in that database
        $response = $this->performRestRequest($databaseIdentifier);
        $this->assertNotEmpty($response, 'Database does not contain any Documents');
        $this->assertEquals($testDocument2[Constants::DATA_ID_KEY], $response[0][Constants::DATA_ID_KEY]);
        $this->assertEquals($testDocument2['name'], $response[0]['name']);
        $this->assertEquals($testDocument2['ip'], $response[0]['ip']);
        $this->assertEquals($testDocument2['os'], $response[0]['os']);


        // Delete a Document
        $response = $this->performRestRequest(
            $databaseIdentifier.'/'.$documentIdentifier2,
            'DELETE',
            $testDocument1
        );
        $this->assertNotEquals(false, $response, 'Could not delete the Document');
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals(sprintf('Document "%s" deleted', $documentIdentifier2), $response['message']);


        // Delete a Document again should fail
        $response = $this->performRestRequest(
            $databaseIdentifier.'/'.$documentIdentifier2,
            'DELETE',
            $testDocument1
        );
        $this->assertSame(false, $response);


        // List Documents in that database
        $response = $this->performRestRequest($databaseIdentifier);
        $this->assertEmpty($response);


        // Delete the database
        $response = $this->performRestRequest($databaseIdentifier, 'DELETE');
        $this->assertArrayHasKey('message', $response, 'Could not delete the Database');
        $this->assertEquals(sprintf('Database "%s" deleted', $databaseIdentifier), $response['message']);


        // The database should not exist anymore
        $response = $this->performRestRequest($databaseIdentifier);
        $this->assertSame(false, $response, 'Database should not exist anymore');


        // Shutdown the server
        $response = $this->performRestRequest('_shutdown', 'POST');
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals('Server is going to shut down', $response['message']);
        sleep(1);


        // The server should not send the welcome message
        $response = $this->performRestRequest('');
        $this->assertSame(false, $response);

        $this->assertFileNotExists($expectedPath);
        if (file_exists($expectedPath)) {
            unlink($expectedPath);
        }
    }

    /**
     * @test
     */
    public function letItRockTest()
    {
        $databaseIdentifier = $this->databaseIdentifier;


        $this->startServer(40);

        // Create a database
        $response = $this->performRestRequest($databaseIdentifier, 'PUT');
        $this->assertNotEquals(false, $response);
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals(sprintf('Database "%s" created', $databaseIdentifier), $response['message']);


        // Create Documents
        $i = 0;
        while (++$i < 1000) {
            $documentHostName   = 'database'.$i.'.my-servers.local';
            $documentIdentifier = md5($documentHostName);

            $testDocument = array(
                'id'   => $documentIdentifier,
                'host' => $documentHostName,
                'name' => 'Database 0'.$i,
                'ip'   => '192.168.45.107',
                'os'   => 'CunddOS',
            );

            $response = $this->performRestRequest($databaseIdentifier, 'POST', $testDocument);
            $this->assertEquals($testDocument['name'], $response['name']);
            $this->assertEquals($testDocument['id'], $response['id']);
            $this->assertEquals($testDocument['ip'], $response['ip']);
            $this->assertEquals($testDocument['os'], $response['os']);

            $response = $this->performRestRequest($databaseIdentifier.'/'.$documentIdentifier, 'GET');
            $this->assertTrue($response !== false);
            $this->assertEquals($testDocument['id'], $response['id']);
        }

        // List Documents in that database
        $response = $this->performRestRequest($databaseIdentifier);
        $this->assertNotEmpty($response);

        // Delete the database
        $response = $this->performRestRequest($databaseIdentifier, 'DELETE');
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals(sprintf('Database "%s" deleted', $databaseIdentifier), $response['message']);

        // Shutdown the server
        $response = $this->performRestRequest('_shutdown', 'POST');
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
     *
     * @return mixed|string
     */
    protected function performRestRequest($request, $method = 'GET', $arguments = array(), $jsonContent = false)
    {
        $url = sprintf('http://127.0.0.1:%d/%s', $this->portForTestServer, $request);

        if ($jsonContent) {
            $content     = json_encode($jsonContent);
            $contentType = 'application/json';
        } else {
            $content     = http_build_query($arguments);
            $contentType = 'application/x-www-form-urlencoded';
        }

        $headers = array(
            'Content-Type: '.$contentType,
            'Content-Length: '.strlen($content),
        );

        //printf('Request %s %d %s' . PHP_EOL, $method, strlen($content), $url);


        //if (is_callable('curl_init')) {
        //    return $this->performRestRequestCurl($url, $method, $headers, $content);
        //}

        return $this->performRestRequestFopen($url, $method, $headers, $content);
    }

    /**
     * Performs a REST request CURL
     *
     * @param string $request
     * @param string $method
     * @param array  $headers
     * @param string $content
     *
     * @return mixed
     */
    protected function performRestRequestCurl($request, $method = 'GET', $headers = array(), $content = '')
    {
        $ch = curl_init($request);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        curl_close($ch);

        if ($response) {
            return json_decode($response, true);
        }

        return $response;
    }

    /**
     * Performs a REST request using file_get_contents
     *
     * @param string $request
     * @param string $method
     * @param array  $headers
     * @param string $content
     *
     * @return mixed
     */
    protected function performRestRequestFopen($request, $method = 'GET', $headers = array(), $content = '')
    {
        $options  = array(
            'http' => array(
                'header'  => implode("\r\n", $headers),
                'method'  => $method,
                'content' => $content,
            ),
        );
        $context  = stream_context_create($options);
        $response = @file_get_contents($request, false, $context);
        if ($response) {
            return json_decode($response, true);
        }

        return $response;
    }

    /**
     * Start the server
     *
     * @param int $autoShutdownTime
     */
    protected function startServer($autoShutdownTime = 7)
    {
        // Start the server
        $configurationManager = ConfigurationManager::getSharedInstance();
        $serverBinPath        = $configurationManager->getConfigurationForKeyPath('binPath').'server';
        $phpBinPath           = $configurationManager->getConfigurationForKeyPath('phpBinPath');
        $phpIniFile           = php_ini_loaded_file();
        $commandParts         = array(
            $phpBinPath,
            $phpIniFile ? '-c'.$phpIniFile : '',
            escapeshellcmd(sprintf('"%s"', $serverBinPath)),
            sprintf('--port=%d', $this->portForTestServer),
        );
        if ($autoShutdownTime > -1) {
            $commandParts[] = '--test='.$autoShutdownTime; // Run the server in test mode
        }
        $commandParts[] = '> /dev/null &'; // Run the server in the background

        printf('Run %s'.PHP_EOL, implode(' ', $commandParts));
        exec(implode(' ', $commandParts), $output, $returnValue);


        // Wait for the server to boot
        sleep(1);
    }
}
