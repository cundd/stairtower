<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 30.08.14
 * Time: 13:09
 */

namespace Cundd\PersistentObjectStore\Formatter;

/**
 * Abstract formatter
 *
 * @package Cundd\PersistentObjectStore\Formatter
 */
abstract class AbstractFormatter implements FormatterInterface {
	/**
	 * @var array
	 */
	protected $configuration;

	/**
	 * Sets the configuration for the formatter
	 *
	 * @param $configuration
	 * @return $this
	 */
	public function setConfiguration($configuration) {
		$this->configuration = $configuration;
	}
} 