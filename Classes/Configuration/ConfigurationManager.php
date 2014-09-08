<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 25.08.14
 * Time: 22:00
 */

namespace Cundd\PersistentObjectStore\Configuration;
use Cundd\PersistentObjectStore\Utility\ObjectUtility;

/**
 * Configuration Manager class
 *
 * @package Cundd\PersistentObjectStore\Configuration
 */
class ConfigurationManager implements ConfigurationManagerInterface {
	/**
	 * Configuration as array
	 *
	 * @var array
	 */
	protected $configuration;

	/**
	 * Shared instance
	 *
	 * @var ConfigurationManagerInterface
	 */
	static protected $sharedInstance;

	function __construct() {
		$configurationReader = new ConfigurationReader();
		$this->configuration = array_merge_recursive(array(
			'dataPath' => __DIR__ . '/../../Tests/Resources/',
			'writeDataPath' => __DIR__ . '/../../Data/'
		), $configurationReader->readConfigurationFiles());

		self::$sharedInstance = $this;
	}


	/**
	 * Returns the configuration for the given key path
	 *
	 * @param string $keyPath
	 * @return mixed
	 */
	public function getConfigurationForKeyPath($keyPath) {
		return ObjectUtility::valueForKeyPathOfObject($keyPath, $this->configuration);
	}

	/**
	 * Sets the configuration for the given key path
	 *
	 * @param string $keyPath
	 * @param mixed  $value
	 * @return $this
	 */
	public function setConfigurationForKeyPath($keyPath, $value) {
		throw new \UnexpectedValueException('Not implemented');
		return $this;
	}

	/**
	 * Returns the shared instance
	 *
	 * @return ConfigurationManagerInterface
	 */
	static public function getSharedInstance() {
		if (!self::$sharedInstance) {
			new static();
		}
		return self::$sharedInstance;
	}

} 