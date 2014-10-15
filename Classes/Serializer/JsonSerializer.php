<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 15.08.14
 * Time: 21:21
 */

namespace Cundd\PersistentObjectStore\Serializer;

/**
 * Class to serialize data to be sent
 *
 * @package Cundd\PersistentObjectStore
 */
class JsonSerializer implements SerializerInterface {
	/**
	 * Serialize the given data
	 *
	 * @param mixed $data
	 * @throws \Cundd\PersistentObjectStore\Serializer\Exception if the data could not be serialized
	 * @return string
	 */
	public function serialize($data) {
		$serializedData = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		if ($serializedData === FALSE) throw $this->_createExceptionFromLastError();
		return $serializedData;
	}

	/**
	 * Unserialize the given data
	 *
	 * @param string $string
	 * @throws \Cundd\PersistentObjectStore\Serializer\Exception if the data could not be unserialized
	 * @return mixed
	 */
	public function unserialize($string) {
		// Just return NULL if the input is "null", to distinguish NULL and invalid inputs (which decode to NULL)
		if ($string === 'null') {
			return NULL;
		}
		$data = json_decode($string, TRUE);
		if ($data === NULL) throw $this->_createExceptionFromLastError();
		return $data;
	}


	/**
	 * Returns an exception describing the last JSON error
	 *
	 * @return Exception
	 */
	protected function _createExceptionFromLastError() {
		if (!function_exists('json_last_error_msg')) {
			switch (json_last_error()) {
				case JSON_ERROR_DEPTH:
					$errorMessage = 'Maximum stack depth exceeded';
					break;
				case JSON_ERROR_STATE_MISMATCH:
					$errorMessage = 'Underflow or the modes mismatch';
					break;
				case JSON_ERROR_CTRL_CHAR:
					$errorMessage = 'Unexpected control character found';
					break;
				case JSON_ERROR_SYNTAX:
					$errorMessage = 'Syntax error, malformed JSON';
					break;
				case JSON_ERROR_UTF8:
					$errorMessage = 'Malformed UTF-8 characters, possibly incorrectly encoded';
					break;
				default:
					$errorMessage = 'Unknown JSON error';
			}
		} else {
			$errorMessage = json_last_error_msg();
		}
		return new Exception($errorMessage, json_last_error());
	}

} 