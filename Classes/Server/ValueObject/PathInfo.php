<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 10.10.14
 * Time: 14:49
 */

namespace Cundd\PersistentObjectStore\Server\ValueObject;
use Cundd\PersistentObjectStore\Utility\GeneralUtility;


/**
 * Object that holds information about an parsed request path
 *
 * @package Cundd\PersistentObjectStore\Server\ValueObject
 */
class PathInfo {
	/**
	 * Identifier for the database
	 *
	 * @var string
	 */
	protected $databaseIdentifier = '';

	/**
	 * Identifier for the Data instance
	 *
	 * @var string
	 */
	protected $dataIdentifier = '';

	/**
	 * Create a new Path Info object
	 *
	 * @param $dataIdentifier
	 * @param $databaseIdentifier
	 */
	function __construct($dataIdentifier, $databaseIdentifier) {
		if ($dataIdentifier) GeneralUtility::assertDataIdentifier($dataIdentifier);
		if ($databaseIdentifier) GeneralUtility::assertDatabaseIdentifier($databaseIdentifier);
		$this->dataIdentifier     = $dataIdentifier;
		$this->databaseIdentifier = $databaseIdentifier;
	}


	/**
	 * Returns the identifier for the Data instance
	 *
	 * @return string
	 */
	public function getDataIdentifier() {
		return $this->dataIdentifier;
	}

	/**
	 * Return the identifier for the database
	 *
	 * @return string
	 */
	public function getDatabaseIdentifier() {
		return $this->databaseIdentifier;
	}
}