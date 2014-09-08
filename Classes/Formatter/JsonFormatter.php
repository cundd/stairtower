<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 30.08.14
 * Time: 13:09
 */

namespace Cundd\PersistentObjectStore\Formatter;
use Cundd\PersistentObjectStore\DataInterface;

/**
 * Class JsonFormatter
 *
 * @package Cundd\PersistentObjectStore\Formatter
 */
class JsonFormatter extends AbstractFormatter {
	/**
	 * Formats the given input model(s)
	 *
	 * @param DataInterface|array<DataInterface> $inputModel
	 * @return string
	 */
	public function format($inputModel) {
		$foundData = array();

		/** @var DataInterface $dataObject */
		foreach ($inputModel as $dataObject) {
			$foundData[] = $dataObject->getData();
		}
		return json_encode($foundData);
		return json_encode($inputModel, JSON_FORCE_OBJECT);
	}

} 