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
	 * JSON welcome message
	 */
	const MESSAGE_JSON_WELCOME = '♜ - STAIRTOWER - PERSISTENT OBJECT STORE';

	/**
	 * CLI welcome message
	 */
	const MESSAGE_CLI_WELCOME = <<<WELCOME


                        /\
                       /  \
                      /____\
      __________      |    |
    /__________/\     |[]_ |
   /__________/()\    |   -|_
  /__________/    \   |    |
  | [] [] [] | [] |  _|    |
  |   ___    |    |   |–_  |
  |   |_| [] | [] |   |  –_|

         STAIRTOWER
   PERSISTENT OBJECT STORE
    a home for your data

WELCOME;

	/**
	 * Version number
	 */
	const VERSION = '0.0.1';

	/**
	 * Key used to store meta data in JSON
	 */
	const DATA_META_KEY = '__meta';

	/**
	 * Key used to store the global unique identifier in JSON
	 */
	const DATA_GUID_KEY = 'guid';

	/**
	 * Key used to store the identifier in JSON
	 */
	const DATA_ID_KEY = '_id';

	/**
	 * Key used to store the database identifier in JSON
	 */
	const DATA_DATABASE_KEY = 'database';
} 