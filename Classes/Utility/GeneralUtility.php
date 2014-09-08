<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 25.08.14
 * Time: 21:40
 */

namespace Cundd\PersistentObjectStore\Utility;
use Cundd\PersistentObjectStore\Exception\InvalidDatabaseIdentifierException;

/**
 * Interface GeneralUtilityInterface
 *
 * @package Cundd\PersistentObjectStore\Utility
 */
abstract class GeneralUtility {
	/**
	 * Checks if the given identifier is valid
	 *
	 * @param string $identifier
	 * @throws InvalidDatabaseIdentifierException if the database isn't valid
	 */
	static public function assertDatabaseIdentifier($identifier) {
		if (!preg_match('(^([a-zA-Z]{1}[a-zA-Z0-9_\-]{0,})$)', $identifier)) throw new InvalidDatabaseIdentifierException("Invalid database identifier '$identifier'", 1408996075);
	}
} 