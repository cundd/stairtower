<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 30.08.14
 * Time: 11:47
 */

namespace Cundd\PersistentObjectStore\Formatter;
use Cundd\PersistentObjectStore\DataInterface;

/**
 * Interface for data formatter responsible to transform Data objects into matching string representations
 *
 * @package Cundd\PersistentObjectStore\Formatter
 */
interface FormatterInterface {
	/**
	 * Sets the configuration for the formatter
	 *
	 * @param $configuration
	 * @return $this
	 */
	public function setConfiguration($configuration);

	/**
	 * Formats the given input model(s)
	 *
	 * @param DataInterface|array<DataInterface> $inputModel
	 * @return string
	 */
	public function format($inputModel);
} 