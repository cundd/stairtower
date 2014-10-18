<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 31.08.14
 * Time: 16:39
 */

namespace Cundd\PersistentObjectStore;
use Cundd\PersistentObjectStore\Domain\Model\Data;
use Cundd\PersistentObjectStore\Domain\Model\DataInterface;

/**
 * Abstract data based test case
 *
 * @package Cundd\PersistentObjectStore
 */
class AbstractDataBasedCase extends AbstractCase {
	/**
	 * Returns the raw test data
	 *
	 * @return array
	 */
	public function getAllTestData() {
		return json_decode(file_get_contents(__DIR__ . '/../Resources/contacts.json'), TRUE);
	}

	/**
	 * Returns the test data as objects
	 *
	 * @return array
	 */
	public function getAllTestObjects() {
		$allTestData = $this->getAllTestData();
		$allTestObjects = array();
		foreach ($allTestData as $currentTestData) {
			$currentObject = new Data($currentTestData, 'contacts');
			$allTestObjects[$currentObject->getGuid()] = $currentObject;
		}
		return $allTestObjects;
	}

	/**
	 * Returns the number of items in the raw test data
	 *
	 * @return array
	 */
	public function countAllTestData() {
		return count($this->getAllTestData());
	}

	/**
	 * @param $database
	 * @return array
	 */
	public function databaseToDataArray($database) {
		$foundData = array();

		/** @var DataInterface $dataObject */
		foreach ($database as $dataObject) {
			$foundData[] = $dataObject->getData();
		}
		return $foundData;
	}
} 