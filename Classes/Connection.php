<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 30.08.14
 * Time: 14:34
 */

namespace Cundd\PersistentObjectStore;


use Doctrine\Common\EventManager;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Query\Expression\ExpressionBuilder;

class Connection extends \Doctrine\DBAL\Connection {
	/**
	 * @var ExpressionBuilder
	 * @Inject
	 */
	protected $_expr;

	/**
	 * Initializes a new instance of the Connection class.
	 *
	 * @param array                              $params       The connection parameters.
	 * @param \Doctrine\DBAL\Driver              $driver       The driver to use.
	 * @param \Doctrine\DBAL\Configuration|null  $config       The configuration, optional.
	 * @param \Doctrine\Common\EventManager|null $eventManager The event manager, optional.
	 *
	 * @throws \Doctrine\DBAL\DBALException
	 */
	public function __construct(array $params, Driver $driver, Configuration $config = NULL, EventManager $eventManager = NULL) {
		$this->_driver = $driver;
//		$this->_params = $params;

		if (isset($params['pdo'])) {
			$this->_conn = $params['pdo'];
//			$this->_isConnected = true;
		}

		// Create default config and event manager if none given
		if ( ! $config) {
			$config = new Configuration();
		}

		if ( ! $eventManager) {
			$eventManager = new EventManager();
		}

		$this->_config = $config;
		$this->_eventManager = $eventManager;

		// Should have been injected
		$this->_expr = new ExpressionBuilder($this);

//		if ( ! isset($params['platform'])) {
//			$this->_platform = $driver->getDatabasePlatform();
//		} else if ($params['platform'] instanceof Platforms\AbstractPlatform) {
//			$this->_platform = $params['platform'];
//		} else {
//			throw DBALException::invalidPlatformSpecified();
//		}
//
//		$this->_platform->setEventManager($eventManager);
//
//		$this->_transactionIsolationLevel = $this->_platform->getDefaultTransactionIsolationLevel();
	}
}