<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 25.08.14
 * Time: 21:30
 */

namespace Cundd\PersistentObjectStore\Domain\Model;

use Cundd\PersistentObjectStore\Filter\Filter;
use Cundd\PersistentObjectStore\Utility\GeneralUtility;

/**
 * Database class which holds the Data instances
 *
 * Implementation with object creation all at once.
 *
 * @package Cundd\PersistentObjectStore\Domain\Model
 */
class Database extends \SplObjectStorage {
	/**
	 * Database identifier
	 *
	 * @var string
	 */
	protected $identifier = '';

	/**
	 * Raw data array
	 *
	 * @var array
	 */
	protected $rawData = array();


	/**
	 * Creates a new database
	 *
	 * @param string $identifier
	 * @param array $rawData
	 */
	function __construct($identifier, $rawData = array()) {
		GeneralUtility::assertDatabaseIdentifier($identifier);
		$this->identifier = $identifier;

		if ($rawData) {
			$this->setRawData($rawData);
		}
	}

	/**
	 * Returns the database identifier
	 *
	 * @return string
	 */
	public function getIdentifier() {
		return $this->identifier;
	}

	/**
	 * Add all objects from the collection to the database
	 *
	 * @param array|\Traversable $collection
	 */
	public function attachAll($collection) {
		foreach ($collection as $element) {
			$this->attach($element);
		}
	}

	/**
	 * Filters the database using the given comparisons
	 *
	 * @param array $comparisons
	 * @return \Cundd\PersistentObjectStore\Filter\FilterResultInterface
	 */
	public function filter($comparisons) {
		$filter = new Filter($comparisons);
		return $filter->filterCollection($this);
	}


	/**
	 * Sets the raw data
	 *
	 * [Optional]
	 * @param $rawDataCollection
	 */
	public function setRawData($rawDataCollection){
		$this->rawData = $rawDataCollection;

		foreach ($rawDataCollection as $rawData) {
			$dataObject = new Data();
			$dataObject->setData($rawData);

			$dataObject->setDatabaseIdentifier($this->getIdentifier());
			$dataObject->setId(isset($rawMetaData['id']) ? $rawMetaData['id'] : NULL);
			$dataObject->setCreationTime(isset($rawMetaData['creation_time']) ? $rawMetaData['creation_time'] : NULL);
			$dataObject->setModificationTime(isset($rawMetaData['modification_time']) ? $rawMetaData['modification_time'] : NULL);
			$this->attach($dataObject);
		}
	}


}