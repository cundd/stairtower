<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 17.10.14
 * Time: 12:52
 */

namespace Cundd\PersistentObjectStore;

use Cundd\PersistentObjectStore\Exception\MemoryManagerException;

/**
 * The Memory Manager tries to help managing the used and available memory
 *
 * @package Cundd\PersistentObjectStore
 */
abstract class MemoryManager implements MemoryManagerInterface {
	/**
	 * A collection of objects that are managed by the Memory Manager
	 *
	 * @var array
	 */
	static protected $managedObjects = array();

	/**
	 * A collection tags
	 *
	 * @var array
	 */
	static protected $managedObjectTags = array();

	/**
	 * Returns all registered objects
	 *
	 * @return array
	 */
	static public function getAllObjects() {
		return self::$managedObjects;
	}

	/**
	 * Register the given object for the given identifier
	 *
	 * @param object $object
	 * @param string $identifier
	 * @param array  $tags
	 */
	static public function registerObject($object, $identifier, $tags = array()) {
		if (!is_string($identifier)) throw new MemoryManagerException('Given identifier is not of type string. Maybe the arguments are swapped', 1413544400);
		$identifier = self::prepareIdentifier($identifier);
		self::$managedObjects[$identifier] = $object;

		foreach ($tags as $tag) {
			self::_addIdentifierForTag($identifier, $tag);
		}
	}

	/**
	 * Returns the object for the given identifier or FALSE if it was not found
	 *
	 * @param string $identifier
	 * @return object|bool
	 */
	static public function getObject($identifier) {
		$identifier = self::prepareIdentifier($identifier);
		if (!self::hasObject($identifier)) return FALSE;
		return self::$managedObjects[$identifier];
	}

	/**
	 * Returns if an object for the given identifier is registered
	 *
	 * @param string $identifier
	 * @return object|bool
	 */
	static public function hasObject($identifier) {
		$identifier = self::prepareIdentifier($identifier);
		return isset(self::$managedObjects[$identifier]);
	}

	/**
	 * Frees the object with the given identifier from the Memory Manager
	 *
	 * @param string $identifier
	 */
	static public function free($identifier) {
		$identifier = self::prepareIdentifier($identifier);
		if (!isset(self::$managedObjects[$identifier])) throw new MemoryManagerException(sprintf('No object registered for identifier "%s"', $identifier), 1413543979);

		self::$managedObjects[$identifier] = NULL;
		unset(self::$managedObjects[$identifier]);

		self::_removeIdentifier($identifier);
		self::cleanup();
	}


	/**
	 * Get identifiers with the given tag
	 *
	 * @param string $tag
	 * @param bool   $graceful
	 * @return array
	 */
	static public function getIdentifiersByTag($tag, $graceful = FALSE) {
		if (!isset(self::$managedObjectTags[$tag])) {
			if (!$graceful) throw new MemoryManagerException(sprintf('Tag %s is not found', $tag), 1413544961);
			return array();
		}
		return array_keys(self::$managedObjectTags[$tag]);
	}

	/**
	 * Returns all objects with a given tag
	 *
	 * @param string $tag
	 * @return array
	 */
	static public function getObjectsByTag($tag) {
		$foundObjects     = array();
		$foundIdentifiers = self::getIdentifiersByTag($tag, TRUE);
		foreach ($foundIdentifiers as $identifier) {
			$foundObjects[] = self::getObject($identifier);
		}
		return $foundObjects;
	}

	/**
	 * Free all objects with a given tag
	 *
	 * @param string $tag
	 * @return array
	 */
	static public function freeObjectsByTag($tag) {
		$foundIdentifiers = self::getIdentifiersByTag($tag, TRUE);
		foreach ($foundIdentifiers as $identifier) {
			self::free($identifier);
		}
	}

	/**
	 * Frees all managed objects
	 *
	 * @internal
	 */
	static public function freeAll() {
		$identifiers = array_keys(self::$managedObjects);
		foreach ($identifiers as $identifier) {
			self::free($identifier);
		}
		self::$managedObjects = array();
	}


	/**
	 * Tells the Memory Manager to clean up the memory
	 */
	static public function cleanup() {
		gc_collect_cycles();
	}

	/**
	 * Prepares the given identifier
	 *
	 * @param string $identifier
	 * @return string
	 */
	static public function prepareIdentifier($identifier) {
		if (!is_scalar($identifier)) {
			throw new MemoryManagerException(sprintf(
					'Invalid identifier type %s',
					$identifier === NULL ? 'null' : gettype($identifier)
				),
				1413543979);
		}
		return (string)$identifier;
	}

	/**
	 * @param string $identifier
	 * @param string $tag
	 */
	static protected function _addIdentifierForTag($identifier, $tag) {
		$identifier = self::prepareIdentifier($identifier);
		if (!isset(self::$managedObjectTags[$tag])) {
			self::$managedObjectTags[$tag] = array();
		}
		self::$managedObjectTags[$tag][$identifier] = TRUE;
	}

	/**
	 * @param string $identifier
	 */
	static protected function _removeIdentifier($identifier) {
		$identifier = self::prepareIdentifier($identifier);
		foreach (self::$managedObjectTags as $tag => $values) {
			unset(self::$managedObjectTags[$tag][$identifier]);
		}
	}
} 