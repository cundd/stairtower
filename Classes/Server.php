<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 15.08.14
 * Time: 19:38
 */

namespace Cundd\PersistentObjectStore;
use Cundd\PersistentObjectStore\DataAccess\Coordinator;
use Cundd\PersistentObjectStore\Domain\Model\Data;
use Cundd\PersistentObjectStore\Driver\Connection;
use Cundd\PersistentObjectStore\Driver\Driver;
use Cundd\PersistentObjectStore\Serializer\JsonSerializer;
use Doctrine\DBAL\DriverManager;

/**
 * Class Server
 *
 * @package Cundd\PersistentObjectStore
 */
class Server {
	/**
	 * Port number to listen on
	 *
	 * @var int
	 */
	protected $port = 1337;

	/**
	 * IP to listen on
	 *
	 * @var string
	 */
	protected $ip = '0.0.0.0';

	/**
	 * Data Access Coordinator
	 *
	 * @var \Cundd\PersistentObjectStore\DataAccess\CoordinatorInterface
	 * @Inject
	 */
	protected $coordinator;

	/**
	 * JSON serializer
	 *
	 * @var \Cundd\PersistentObjectStore\Serializer\JsonSerializer
	 * @Inject
	 */
	protected $serializer;

	/**
	 * Formatter
	 *
	 * @var \Cundd\PersistentObjectStore\Formatter\JsonFormatter
	 * @Inject
	 */
	protected $formatter;

	/**
	 * @var Server
	 */
	static protected $_sharedServer;

	/**
	 * Starts the request loop
	 */
	public function start() {

		$i = 0;

		$app = function ($request, $response) use (&$i) {
			$i++;

			$text = "This is request number $i.\n";
			$headers = array('Content-Type' => 'text/plain');

			var_dump($request);

			$response->writeHead(200, $headers);
			$response->end($text);
		};


		$eventLoop = \React\EventLoop\Factory::create();


//		$eventLoop->addPeriodicTimer(2, function() {
//			var_dump('hallo');
//		});

		$coordinator = $this->coordinator;
		$serializer = $this->serializer;
		$formatter = $this->formatter;


		$socketServer = new \React\Socket\Server($eventLoop);
		$socketServer->on('connection', function ($connection) use ($coordinator, $serializer, $formatter) {
			/** @var \React\Socket\Connection $connection */

			var_dump(get_class($connection));
			$connection->write("Hello there!\n");
			$connection->write("Welcome to this amazing server!\n");
			$connection->write("Here's a tip: don't say anything.\n");

			$connection->on('data', function ($data) use ($connection, $coordinator, $serializer, $formatter) {
				var_dump('You said', $data);
				$format = 'json';
				$connection->write("You said: $data\n");

				$data = trim($data);




//				$database = $coordinator->getDataByDatabase($data);

//				$conn = DriverManager::getConnection(array('driverClass' => 'Cundd\\PersistentObjectStore\\Driver\\Driver'));
//				$queryBuilder = $conn->createQueryBuilder();

				$queryBuilder = new \Doctrine\DBAL\Query\QueryBuilder(new \Cundd\PersistentObjectStore\Connection(array(), new Driver()));

//				$queryBuilder
//					->select('id', 'name')
//					->from('contacts', 'users')
//					->where('email = ?')
//					->setParameter(0, 'spm@cundd.net')
//				;
				$queryBuilder->select('c')
					->innerJoin('c.lastName', 'contacts', 'ON', $queryBuilder->expr()->eq('p.lastName', ':lastName'))
					->where('c.email = :email');

				$queryBuilder->setParameters(array(
					'email' => 'spm@cundd.net',
					'lastName' => 'Jobs',
				));

				/** @var \Cundd\PersistentObjectStore\DataAccess\CoordinatorInterface $coordinator */
				$database = $coordinator->getDataByQuery($queryBuilder);
				$connection->write($formatter->format($database) . "\n");
//				$connection->write($serializer->serialize($foundData) . "\n");


				return $foundData;


//				$connection->close();
			});
		});


		$socketServer->listen($this->port, $this->ip);

//		$http = new \React\Http\Server($socketServer);
//		$http->on('request', $app);



		$eventLoop->run();





	}

	/**
	 * Sets the IP to listen on
	 *
	 * @param string $ip
	 * @return $this
	 */
	public function setIp($ip) {
		$this->ip = $ip;
		return $this;
	}

	/**
	 * Returns the IP to listen on
	 *
	 * @return string
	 */
	public function getIp() {
		return $this->ip;
	}

	/**
	 * Sets the port number to listen on
	 * @param int $port
	 * @return $this
	 */
	public function setPort($port) {
		$this->port = $port;
		return $this;
	}

	/**
	 * Returns the port number to listen on
	 * @return int
	 */
	public function getPort() {
		return $this->port;
	}

	/**
	 * Returns the shared server instance
	 *
	 * @return Server
	 */
	static public function sharedServer() {
		if (!self::$_sharedServer) {
			self::$_sharedServer = new static();
		}
		return self::$_sharedServer;
	}

} 