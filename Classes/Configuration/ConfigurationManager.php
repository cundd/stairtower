<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 25.08.14
 * Time: 22:00
 */

namespace Cundd\PersistentObjectStore\Configuration;
use Cundd\PersistentObjectStore\RuntimeException;
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
			'basePath' => __DIR__ . '/../../',
			'dataPath' => __DIR__ . '/../../var/Data/',
			'writeDataPath' => __DIR__ . '/../../var/Data/'
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
		if (strpos($keyPath, '.') !== FALSE) {
			throw new RuntimeException('Dot notation is currently not supported');
		}
		$this->configuration[$keyPath] = $value;
//		ObjectUtility::setValueForKeyPathOfObject($value, $keyPath, $this->configuration);
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