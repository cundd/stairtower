<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 25.08.14
 * Time: 21:40
 */

/**
 * Interface GeneralUtilityInterface
 *
 * @package Cundd\PersistentObjectStore\Utility
 */
class GeneralUtility {
	/**
	 * Checks if the given database identifier is valid
	 *
	 * @param string $identifier
	 * @throws Exception
	 */
	static public function assertDatabaseIdentifier($identifier) {
		if (!preg_match('(^([a-zA-Z]{1}[a-zA-Z0-9_\-]{0,})$)', $identifier)) throw new Exception("Invalid database identifier '$identifier'", 1408996075);
	}

	/**
	 * Checks if the given data identifier is valid
	 *
	 * @param string $identifier
	 * @throws Exception if the database isn't valid
	 */
	static public function assertDataIdentifier($identifier) {
		if (!preg_match('(^([a-zA-Z0-9]{1}[a-zA-Z0-9_\-\.@]{0,})$)', $identifier)) throw new Exception("Invalid data identifier '$identifier'", 1412889537);
	}

	/**
	 * Checks if the given method is a valid HTTP method
	 *
	 * @param string $method
	 * @throws Exception
	 */
	static public function assertRequestMethod($method) {
		if (!in_array($method, array('GET', 'POST', 'PUT', 'DELETE', 'HEAD'))) throw new Exception("Invalid method '$method'", 1413052000);
	}

	/**
	 * Format the given memory amount
	 *
	 * @param int $bytes
	 * @param int $precision
	 * @return string
	 */
	static public function formatBytes($bytes, $precision = 2) {
		$units = array('B', 'KB', 'MB', 'GB', 'TB');

		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);

		// Uncomment one of the following alternatives
		$bytes /= pow(1024, $pow);
		// $bytes /= (1 << (10 * $pow));

		return round($bytes, $precision) . ' ' . $units[$pow];
	}

	/**
	 * Returns the integer value of the given variable or NULL if it is no valid integer
	 *
	 * @param $var
	 * @return int|null
	 */
	static public function validateInteger($var) {
		if (is_integer($var) || ((string)(int)$var === $var)) {
			return intval($var);
		}
		return NULL;
	}

	/**
	 * Transform the underscored_string to camelCase
	 *
	 * @param string $underscoreString
	 * @return string
	 */
	static public function underscoreToCamelCase($underscoreString) {
		$prefix = $underscoreString[0] === '_' ? '_' : '';
		return $prefix . lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $underscoreString))));
	}

	/**
	 * Tries to convert the given value to a string
	 *
	 * @param mixed $value
	 * @return bool|string Returns the string representation or FALSE on error
	 */
	static public function toString($value) {
		switch (TRUE) {
			case is_null($value):
				return '';

			case is_scalar($value):
				return (string) $value;

			case is_resource($value):
				return (string) $value;

			case is_array($value):
				return implode(' ', $value);

			case is_object($value) && method_exists($value, '__toString'):
				return (string) $value;
		}
		return FALSE;
	}
}

$file = 'people';
$file = 'contacts';
$file = 'cars';
$file = 'yvi';
if ($argc > 1) {
	$file = $argv[1];
}
$file = __DIR__ . '/var/Data/' . $file . '.json';
$fileContents = '';

$csvPath = __DIR__ . '/memory_samples.csv';
$fileHandle = fopen($csvPath, 'a');

$standardMemoryUsage = memory_get_usage(TRUE);

printf('Memory: %s Peak: %s' . PHP_EOL, GeneralUtility::formatBytes(memory_get_usage(TRUE) - $standardMemoryUsage), GeneralUtility::formatBytes(memory_get_peak_usage(TRUE) - $standardMemoryUsage));
printf('File size: %s' . PHP_EOL, GeneralUtility::formatBytes(filesize($file)));
printf('Memory: %s Peak: %s' . PHP_EOL, GeneralUtility::formatBytes(memory_get_usage(TRUE) - $standardMemoryUsage), GeneralUtility::formatBytes(memory_get_peak_usage(TRUE) - $standardMemoryUsage));
file_get_contents($file);
printf('Memory: %s Peak: %s' . PHP_EOL, GeneralUtility::formatBytes(memory_get_usage(TRUE) - $standardMemoryUsage), GeneralUtility::formatBytes(memory_get_peak_usage(TRUE) - $standardMemoryUsage));

$fileContents = file_get_contents($file);
printf('Memory: %s Peak: %s' . PHP_EOL, GeneralUtility::formatBytes(memory_get_usage(TRUE) - $standardMemoryUsage), GeneralUtility::formatBytes(memory_get_peak_usage(TRUE) - $standardMemoryUsage));
printf('Memory ratio: memory/filesize = %s' . PHP_EOL, (memory_get_usage(TRUE) - $standardMemoryUsage) / filesize($file));

$json = json_decode(file_get_contents($file), TRUE);
printf('Memory: %s Peak: %s' . PHP_EOL, GeneralUtility::formatBytes(memory_get_usage(TRUE) - $standardMemoryUsage), GeneralUtility::formatBytes(memory_get_peak_usage(TRUE) - $standardMemoryUsage));
printf('Memory ratio: memory/filesize = %s' . PHP_EOL, (memory_get_usage(TRUE) - $standardMemoryUsage) / filesize($file));
printf('Memory ratio: memory/strlen = %s' . PHP_EOL, (memory_get_usage(TRUE) - $standardMemoryUsage) / strlen($fileContents));
printf('Memory ratio: memory/braces = %s' . PHP_EOL, (memory_get_usage(TRUE) - $standardMemoryUsage) / substr_count($fileContents, '{'));
printf('Memory ratio: memory/comma = %s' . PHP_EOL, (memory_get_usage(TRUE) - $standardMemoryUsage) / substr_count($fileContents, ','));
printf('Memory ratio: log(memory)/log(comma) = %s' . PHP_EOL, log(memory_get_usage(TRUE) - $standardMemoryUsage) / log(filesize($file)));

$toMb = 1024 * 1024;
//fputcsv($fileHandle, array(filesize($file) / $toMb, (float)(memory_get_usage(TRUE) - $standardMemoryUsage) / $toMb, (float)(memory_get_peak_usage(TRUE) - $standardMemoryUsage) / $toMb));
fputcsv($fileHandle, array(filesize($file), (float)(memory_get_usage(TRUE) - $standardMemoryUsage), (float)(memory_get_peak_usage(TRUE) - $standardMemoryUsage)));