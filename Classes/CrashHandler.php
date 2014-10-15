<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 15.10.14
 * Time: 17:41
 */

namespace Cundd\PersistentObjectStore;


use Cundd\PersistentObjectStore\Configuration\ConfigurationManager;
use Cundd\PersistentObjectStore\DataAccess\Coordinator;
use Cundd\PersistentObjectStore\Domain\Model\Database;
use Cundd\PersistentObjectStore\Utility\GeneralUtility;
use DateTime;

/**
 * Crash handler that tries to rescue the in-memory databases
 *
 * @package Cundd\PersistentObjectStore
 */
class CrashHandler {
	/**
	 * Defines if the crash handler has been registered
	 *
	 * @var bool
	 */
	static protected $didRegister = FALSE;

	/**
	 * Registers the crash handler
	 */
	static public function register() {
		register_shutdown_function(array(get_called_class(), 'handleCrash'));
	}

	/**
	 * Tries to handle a crashed system
	 */
	static public function handleCrash() {
		$error = error_get_last();
		if ($error !== NULL) {
			// Construct a helpful crash message
			$errorNumber  = intval($error['type']);
			$errorFile    = $error['file'];
			$errorLine    = $error['line'];
			$errorMessage = $error['message'];

			$errorReport   = [];
			$errorReport[] = sprintf('Server crashed with code %d and message "%s" in %s at %s', $errorNumber, $errorMessage, $errorFile, $errorLine);
			$errorReport[] = sprintf('Date/time: %s', static::getTimeWithMicroseconds()->format('Y-m-d H:i:s.u'));
			$errorReport[] = sprintf('Current memory usage: %s', GeneralUtility::formatBytes(memory_get_usage(TRUE)));
			$errorReport[] = sprintf('Peak memory usage: %s', GeneralUtility::formatBytes(memory_get_peak_usage(TRUE)));

			// Try to rescue data
			$errorReport[] = static::rescueData();

			// Output and save the information
			$errorReport = implode(PHP_EOL, $errorReport);
			$errorReportPath = static::getRescueDirectory() . 'CRASH_REPORT.txt';
			file_put_contents($errorReportPath, $errorReport);
			print $errorReport;
		}
	}

	/**
	 * Try to backup data in memory
	 *
	 * @return string Returns a message describing the result
	 */
	static public function rescueData() {
		$resultMessageParts = array();
		$data               = Coordinator::getObjectStore();
		$backupDirectory    = static::getRescueDirectory();
		if ($data) {
			foreach ($data as $databaseIdentifier => $database) {
				$currentData = NULL;
				if ($database instanceof Database) {
					$currentData = $database->getRawData();
				} else if ($database instanceof \Iterator) {
					$currentData = iterator_to_array($database);
				}

				if (!$currentData) {
					$resultMessageParts[] = sprintf('Can not rescue database %s', $databaseIdentifier);
					continue;
				}

				$backupData = NULL;
				$jsonData   = json_encode($currentData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
				if ($jsonData) {
					$backupData = $jsonData;
				} else {
					$backupData = serialize($currentData);
				}


				$backupPath = $backupDirectory . $databaseIdentifier . '.' . ($jsonData ? 'json' : 'bin');
				if (file_put_contents($backupPath, $backupData)) {
					$resultMessageParts[] = sprintf('Created backup of database %s at %s', $databaseIdentifier, $backupPath);
				} else {
					$resultMessageParts[] = sprintf('Can not rescue database %s', $databaseIdentifier);
				}
			}
		} else {
			$resultMessageParts[] = sprintf('Can not find any data to rescue');
		}
		return implode(PHP_EOL, $resultMessageParts);
	}

	/**
	 * Returns the current time with microseconds
	 *
	 * @return DateTime
	 */
	static protected function getTimeWithMicroseconds() {
		$t     = microtime(TRUE);
		$micro = sprintf('%06d', ($t - floor($t)) * 1000000);
		$now   = new DateTime(gmdate('Y-m-d H:i:s.') . $micro);
		return $now;
	}

	/**
	 * Returns the path to store the rescue data in
	 *
	 * @return string
	 */
	static protected function getRescueDirectory() {
		$backupDirectory = ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('rescuePath');
		$backupDirectory .= gmdate('Y-m-d-H-i-s') . '/';
		if (!file_exists($backupDirectory)) {
			mkdir($backupDirectory, 0770, TRUE);
		}
		return $backupDirectory;
	}
} 