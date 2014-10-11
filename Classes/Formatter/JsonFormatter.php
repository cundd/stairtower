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
		if (is_array($inputModel) || $inputModel instanceof \Iterator) {
			$foundData = array();
			/** @var DataInterface $dataObject */
			foreach ($inputModel as $dataObject) {
				$foundData[] = $dataObject->getData();
			}
			return $this->serializer->serialize($foundData);
		} else if (is_scalar($inputModel)) {
			return $this->serializer->serialize(array('message' => $inputModel));
		}
		return $this->serializer->serialize($inputModel);
	}

	/**
	 * Returns the content suffix for the formatter
	 *
	 * @return string
	 */
	public function getContentSuffix() {
		return 'json';
	}

} 