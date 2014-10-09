<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 09.10.14
 * Time: 14:18
 */

namespace Cundd\PersistentObjectStore;

/**
 * Collections of constants used through out the system
 *
 * @package Cundd\PersistentObjectStore
 */
interface Constants {
	/**
	 * Version number
	 */
	const VERSION = '0.1.0';

	/**
	 * Key used to store meta data in JSON
	 */
	const DATA_META_KEY = '__meta';

	/**
	 * Key used to store the global unique identifier in JSON
	 */
	const DATA_GUID_KEY = 'guid';

	/**
	 * Key used to store the database identifier in JSON
	 */
	const DATA_DATABASE_KEY = 'database';
} 