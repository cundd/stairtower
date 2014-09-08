<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 06.09.14
 * Time: 21:09
 */

namespace Cundd\PersistentObjectStore\Utility;

/**
 * Debug utility
 *
 * @package Cundd\Rest\Utility
 */
class DebugUtility {
	/**
	 * Offset to use to get the caller from the backtrace
	 * @var int
	 */
	static protected $backtraceOffset = 1;

	/**
	 * Print debug information about the given values (arg0, arg1, ... argN)
	 *
	 * @param $variable
	 */
	static public function debug($variable) {
		$caller = static::getCaller();
		$htmlOutput = php_sapi_name() !== 'cli';
		$colorOutput = isset($_SERVER['TERM']) && strtolower($_SERVER['TERM']) === 'xterm';

		if ($htmlOutput) echo '<pre class="rest-debug"><code>';

		$variables = func_get_args();
		foreach ($variables as $variable) {
			var_dump($variable);
			echo PHP_EOL;
		}
		if ($htmlOutput) echo '</code>';

		// Debug info
		$file = $caller['file'];
		$line = $caller['line'];
		if ($htmlOutput) {
			echo "<span class='rest-debug-path' style='font-size:9px'><a href='file:$file'>see $file($line)</a></span>";
			echo "</pre>";
		} else if ($colorOutput) {
			echo "\033[0;35m" . "$file($line)" . "\033[0m";
		} else {
			echo "$file($line)";
		}

		if ($htmlOutput) echo '</pre>';
		echo PHP_EOL;
		echo PHP_EOL;
		echo PHP_EOL;
	}
	/**
	 * @see debug()
	 */
	static public function var_dump($variable) {
		$variables = func_get_args();
		static::$backtraceOffset += 2;
		call_user_func_array(array(__CLASS__, 'debug'), $variables);
		static::$backtraceOffset -= 2;
	}

	/**
	 * Returns the caller of the previous method
	 *
	 * @return array
	 */
	static public function getCaller() {
		if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
			$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);
		} else {
			$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		}
		return $backtrace[static::$backtraceOffset];
	}

}