<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 30.08.14
 * Time: 13:09
 */

namespace Cundd\PersistentObjectStore\Formatter;
use Cundd\PersistentObjectStore\Domain\Model\DataInterface;

/**
 * Class JsonFormatter
 *
 * @package Cundd\PersistentObjectStore\Formatter
 */
class JsonFormatter extends AbstractFormatter {
	/**
	 * @var \Cundd\PersistentObjectStore\Serializer\DataInstanceSerializer
	 * @Inject
	 */
	protected $serializer;

	/**
	 * Formats the given input model(s)
	 *
	 * @param DataInterface|array<DataInterface> $inputModel
	 * @return string
	 */
	public function format($inputModel) {
		$foundData = array();

		if (is_array($inputModel) || $inputModel instanceof \Iterator) {
			/** @var DataInterface $dataObject */
			foreach ($inputModel as $dataObject) {
				$foundData[] = $dataObject->getData();
			}
			return $this->serializer->serialize($foundData);
		}
		return $this->serializer->serialize($inputModel);
	}

} 