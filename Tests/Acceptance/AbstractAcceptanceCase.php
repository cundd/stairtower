<?php
declare(strict_types=1);


namespace Cundd\Stairtower\Tests\Acceptance;


use Cundd\Stairtower\Configuration\ConfigurationManager;
use Cundd\Stairtower\Constants;
use Cundd\Stairtower\Tests\HttpRequestClient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

abstract class AbstractAcceptanceCase extends TestCase
{
    /**
     * Specifies the number of documents that should be created in performanceTest()
     *
     * @var integer
     */
    protected $numberOfDocumentsToCreate;

    /**
     * Name of the database to use/create for the test
     *
     * @var string
     */
    protected $databaseIdentifier;

    /**
     * Expected database storage path
     *
     * @var string
     */
    protected $expectedPath;

    /**
     * @var Process
     */
    private $process;

    /**
     * @param int $autoShutdownTime
     * @return Process
     */
    abstract protected function startServer(int $autoShutdownTime = 7): Process;

    protected function setUp()
    {
        parent::setUp();

        $this->databaseIdentifier = $databaseIdentifier = 'test-db-' . time();
        $this->expectedPath = __DIR__ . '/../../var/Data/' . $databaseIdentifier . '.json';
    }

    protected function tearDown()
    {
        $this->process->stop();

        if (file_exists($this->expectedPath)) {
            unlink($this->expectedPath);
        }

        $writeDataPath = ConfigurationManager::getSharedInstance()
                ->getConfigurationForKeyPath('writeDataPath') . $this->databaseIdentifier . '.json';
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
        // fwrite(STDOUT, 'Test database: ' . $this->databaseIdentifier . PHP_EOL);
        $httpClient = new HttpRequestClient($this->getUriForTestServer());

        $databaseIdentifier = $this->databaseIdentifier;

        $document1HostName = 'database01.my-servers.local';
        $documentIdentifier1 = md5($document1HostName);

        $document2HostName = 'web01.my-servers.local';
        $documentIdentifier2 = md5($document2HostName);

        $testDocument1 = [
            Constants::DATA_ID_KEY => $documentIdentifier1,
            'host'                 => $document1HostName,
            'name'                 => 'Database 01',
            'ip'                   => '192.168.45.107',
            'os'                   => 'CunddOS',
        ];
        $testDocument2 = [
            Constants::DATA_ID_KEY => $documentIdentifier2,
            'host'                 => $document2HostName,
            'name'                 => 'Database 02',
            'ip'                   => '192.168.40.127',
            'os'                   => 'CunddOS',
        ];

        $this->process = $this->startServer();

        // Get the welcome message
        $response = $httpClient->performRestRequest('');
        $this->assertTrue($response->isSuccess(), 'Could not get the welcome message');
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals(Constants::MESSAGE_JSON_WELCOME, $response['message']);


        // Get the stats
        $response = $httpClient->performRestRequest('_stats');
        $this->assertTrue($response->isSuccess(), 'Could not get the stats');
        $this->assertArrayHasKey('version', $response, 'Could not get the stats');
        $this->assertArrayHasKey('guid', $response);


        // Create a database
        $response = $httpClient->performRestRequest($databaseIdentifier, 'PUT');
        $this->assertTrue($response->isSuccess(), 'Could not create the Database');
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals(sprintf('Database "%s" created', $databaseIdentifier), $response['message']);


        // List all Databases
        $response = $httpClient->performRestRequest('_all_dbs');
        $this->assertTrue($response->isSuccess(), 'Could not list all Databases');
        $this->assertTrue(in_array($this->databaseIdentifier, $response->getParsedBody()));


        // List Documents in that database
        $response = $httpClient->performRestRequest($databaseIdentifier);
        $this->assertEmpty($response->getParsedBody(), 'Database should be empty');


        // Add a Document
        $response = $httpClient->performRestRequest($databaseIdentifier, 'POST', $testDocument1);
        $this->assertTrue($response->isSuccess());
        $this->assertEquals(
            $testDocument1[Constants::DATA_ID_KEY],
            $response[Constants::DATA_ID_KEY],
            'Could not add the Document'
            . PHP_EOL . $httpClient->getLastCurlCommand()
            . PHP_EOL . var_export($response, true)
        );

        $this->assertEquals($testDocument1['name'], $response['name']);
        $this->assertEquals($testDocument1['ip'], $response['ip']);
        $this->assertEquals($testDocument1['os'], $response['os']);


        // List Documents in that database
        $response = $httpClient->performRestRequest($databaseIdentifier);
        $this->assertInternalType('array', $response->getParsedBody(), 'Could not list the Documents');
        $this->assertNotEmpty(
            $response->getParsedBody(),
            'Document list is empty' . PHP_EOL . $httpClient->getLastCurlCommand() . PHP_EOL
        );
        $responseFirstDocument = $response[0];
        $this->assertEquals($testDocument1[Constants::DATA_ID_KEY], $responseFirstDocument[Constants::DATA_ID_KEY]);
        $this->assertEquals($testDocument1['name'], $responseFirstDocument['name']);
        $this->assertEquals($testDocument1['ip'], $responseFirstDocument['ip']);
        $this->assertEquals($testDocument1['os'], $responseFirstDocument['os']);

        // Add another Document
        $response = $httpClient->performRestRequest($databaseIdentifier, 'POST', $testDocument2);
        $this->assertTrue($response->isSuccess(), 'Could not add another Document');
        $this->assertEquals($testDocument2[Constants::DATA_ID_KEY], $response[Constants::DATA_ID_KEY]);
        $this->assertEquals($testDocument2['name'], $response['name'], 'Could not add another Document');
        $this->assertEquals($testDocument2['ip'], $response['ip']);
        $this->assertEquals($testDocument2['os'], $response['os']);


        // List Documents in that database
        $response = $httpClient->performRestRequest($databaseIdentifier);
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
        $response = $httpClient->performRestRequest($databaseIdentifier . '/_count');
        $this->assertNotEmpty($response, 'Could not count the Documents in the Database');
        $this->assertEquals(2, $response['count']);


        // Update a Document
        $testDocument1['os'] = 'Cundbuntu';
        $response = $httpClient->performRestRequest(
            $databaseIdentifier . '/' . $documentIdentifier1,
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
        $response = $httpClient->performRestRequest($databaseIdentifier);
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
        $response = $httpClient->performRestRequest($databaseIdentifier . '/?os=' . $testDocument1['os']);
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
        $response = $httpClient->performRestRequest(
            $databaseIdentifier . '/' . $documentIdentifier1,
            'DELETE',
            $testDocument1
        );
        $this->assertTrue(
            $response->isSuccess(),
            'Could not delete Document: ' . PHP_EOL . $httpClient->getLastCurlCommand()
        );
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals(sprintf('Document "%s" deleted', $documentIdentifier1), $response['message']);


        // List Documents in that database
        $response = $httpClient->performRestRequest($databaseIdentifier);
        $this->assertNotEmpty($response, 'Database does not contain any Documents');
        $this->assertEquals($testDocument2[Constants::DATA_ID_KEY], $response[0][Constants::DATA_ID_KEY]);
        $this->assertEquals($testDocument2['name'], $response[0]['name']);
        $this->assertEquals($testDocument2['ip'], $response[0]['ip']);
        $this->assertEquals($testDocument2['os'], $response[0]['os']);


        // Delete a Document
        $response = $httpClient->performRestRequest(
            $databaseIdentifier . '/' . $documentIdentifier2,
            'DELETE',
            $testDocument1
        );
        $this->assertTrue($response->isSuccess(), 'Could not delete the Document');
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals(sprintf('Document "%s" deleted', $documentIdentifier2), $response['message']);


        // Delete a Document again should fail
        $response = $httpClient->performRestRequest(
            $databaseIdentifier . '/' . $documentIdentifier2,
            'DELETE',
            $testDocument1,
            $rawResult
        );

        $this->assertSame(
            [
                'message' => sprintf(
                    'Document with identifier "%s" not found in database "%s"',
                    $documentIdentifier2,
                    $databaseIdentifier
                ),
            ],
            $response->getParsedBody(),
            'Failed for request: '
            . PHP_EOL . $httpClient->getLastCurlCommand() . PHP_EOL
            . 'Raw result: ' . var_export($rawResult, true)
        );


        // List Documents in that database
        $response = $httpClient->performRestRequest($databaseIdentifier);
        $this->assertEmpty($response->getParsedBody());


        // Delete the database
        $response = $httpClient->performRestRequest($databaseIdentifier, 'DELETE');
        $this->assertTrue($response->isSuccess(), 'Could not delete the Database');
        $this->assertArrayHasKey('message', $response, 'Could not delete the Database');
        $this->assertEquals(sprintf('Database "%s" deleted', $databaseIdentifier), $response['message']);


        // The database should not exist anymore
        $response = $httpClient->performRestRequest($databaseIdentifier);
        $this->assertFalse($response->isSuccess());
        $this->assertSame(
            [
                'message' => sprintf('Database with identifier "%s" not found', $databaseIdentifier),
            ],
            $response->getParsedBody(),
            'Database should not exist anymore'
        );


        // Shutdown the server
        $response = $httpClient->performRestRequest('_shutdown', 'POST');
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals('Server is going to shut down', $response['message']);
    }

    /**
     * @test
     */
    public function performanceTest()
    {
        //fwrite(STDOUT, 'Test database: ' . $this->databaseIdentifier . PHP_EOL);
        $databaseIdentifier = $this->databaseIdentifier;

        $this->process = $this->startServer(40);

        $httpClient = new HttpRequestClient($this->getUriForTestServer());

        // Create a database
        $response = $httpClient->performRestRequest($databaseIdentifier, 'PUT', null, $rawResult);
        $this->assertTrue(
            $response->isSuccess(),
            "Could not create database $databaseIdentifier"
            . PHP_EOL . $httpClient->getLastCurlCommand()
            . PHP_EOL . 'Raw result: "' . $rawResult . '"'
        );
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals(sprintf('Database "%s" created', $databaseIdentifier), $response['message']);


        // Create Documents
        $i = 0;
        while (++$i < $this->numberOfDocumentsToCreate) {
            $documentHostName = 'database' . $i . '.my-servers.local';
            $documentIdentifier = md5($documentHostName);

            $testDocument = [
                'id'   => $documentIdentifier,
                'host' => $documentHostName,
                'name' => 'Database 0' . $i,
                'ip'   => '192.168.45.107',
                'os'   => 'CunddOS',
            ];

            $response = $httpClient->performRestRequest($databaseIdentifier, 'POST', $testDocument);
            $this->assertTrue(
                $response->isSuccess(),
                sprintf(
                    'Post request #%d failed (HTTP status: %s, HTTP error: "%s")',
                    $i,
                    $response->getStatus(),
                    $response->getError() ? $response->getError()->getMessage() : '(undefined)'
                )
            );
            $this->assertInternalType('array', $response->getParsedBody(), sprintf('Post request #%d failed', $i));
            $this->assertEquals($testDocument['name'], $response['name']);
            $this->assertEquals($testDocument['id'], $response['id']);
            $this->assertEquals($testDocument['ip'], $response['ip']);
            $this->assertEquals($testDocument['os'], $response['os']);

            $response = $httpClient->performRestRequest($databaseIdentifier . '/' . $documentIdentifier, 'GET');
            $this->assertTrue(
                $response->isSuccess(),
                sprintf(
                    'Could not retrieve document #%d (ID %s, HTTP status: %s, HTTP error: "%s")',
                    $i,
                    $databaseIdentifier,
                    $response->getStatus(),
                    $response->getError() ? $response->getError()->getMessage() : '(undefined)'
                )
            );
            $this->assertArrayHasKey('id', $response);
            $this->assertEquals($testDocument['id'], $response['id']);
        }

        // List Documents in that database
        $response = $httpClient->performRestRequest($databaseIdentifier);
        $this->assertNotEmpty($response);

        // Delete the database
        $response = $httpClient->performRestRequest($databaseIdentifier, 'DELETE');
        $this->assertTrue($response->isSuccess(), 'Could not delete Database ' . $databaseIdentifier);
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals(sprintf('Database "%s" deleted', $databaseIdentifier), $response['message']);

        // Shutdown the server
        $response = $httpClient->performRestRequest('_shutdown', 'POST');
        $this->assertTrue($response->isSuccess());
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals('Server is going to shut down', $response['message']);
    }

    /**
     * @return int
     */
    protected function getPortForTestServer()
    {
        return (int)getenv('STAIRTOWER_TEST_SERVER_PORT') ?: 7700;
    }

    /**
     * @return string
     */
    protected function getIpForTestServer()
    {
        return (string)getenv('STAIRTOWER_TEST_SERVER_IP') ?: '127.0.0.1';
    }

    /**
     * @return string
     */
    protected function getUriForTestServer()
    {
        return $this->getIpForTestServer() . ':' . $this->getPortForTestServer();
    }

    /**
     * @return ProcessBuilder
     */
    protected function getProcessBuilder(): ProcessBuilder
    {
        return new ProcessBuilder();
    }
}
