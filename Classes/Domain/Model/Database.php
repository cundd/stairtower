<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 25.08.14
 * Time: 21:30
 */

namespace Cundd\PersistentObjectStore\Domain\Model;

use Cundd\PersistentObjectStore\Utility\GeneralUtility;

/**
 * Database class which holds the Data instances
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

	function __construct($identifier) {
		GeneralUtility::assertDatabaseIdentifier($identifier);
		$this->identifier = $identifier;
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
}