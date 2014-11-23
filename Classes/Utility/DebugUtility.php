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
class DebugUtility
{
    /**
     * Offset to use to get the caller from the backtrace
     *
     * @var int
     */
    public static $backtraceOffset = 1;

    /**
     * @see debug()
     */
    public static function var_dump($variable)
    {
        $variables = func_get_args();
        static::$backtraceOffset += 2;
        call_user_func_array(array(__CLASS__, 'debug'), $variables);
        static::$backtraceOffset -= 2;
    }

    /**
     * Print the given message
     *
     * @param string $message
     * @param mixed  $additional ...
     */
    public static function pl($message, $additional = null)
    {
        $caller      = static::getCaller();
        $htmlOutput  = php_sapi_name() !== 'cli';
        $colorOutput = isset($_SERVER['TERM']) && strtolower($_SERVER['TERM']) === 'xterm';

        if ($htmlOutput) {
            echo '<pre class="rest-debug"><code>';
        }

        if ($additional) {
            $additional = func_get_args();
            array_shift($additional);
        }
        echo vsprintf($message, $additional) . PHP_EOL;
        if ($htmlOutput) {
            echo '</code>';
        }

        // Debug info
        $line = $caller['line'];
        $file = $caller['file'];
        if ($htmlOutput) {
            echo "<span class='rest-debug-path' style='font-size:9px'><a href='file:$file'>see $file($line)</a></span>";
            echo "</pre>";
        } else {
            if ($colorOutput) {
                echo "\033[0;35m" . "$file($line)" . "\033[0m";
            } else {
                echo "($file:$line)";
            }
        }

        if ($htmlOutput) {
            echo '</pre>';
        }
        echo PHP_EOL;
        echo PHP_EOL;
        echo PHP_EOL;
    }

    /**
     * Returns the caller of the previous method
     *
     * @return array
     */
    public static function getCaller()
    {
        static $basePathLength = '';
        if (!$basePathLength) {
            $basePathLength = strlen(realpath(__DIR__ . '/../../'));
        }
        if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);
        } else {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        }
        $caller                 = $backtrace[static::$backtraceOffset];
        $caller['relativePath'] = '.' . substr($caller['file'], $basePathLength);
        return $caller;
    }

    /**
     * Prints the memory usage
     */
    public static function printMemorySample()
    {
        static::$backtraceOffset += 1;
        static::debug(sprintf('Memory: %s (max: %s)',
            GeneralUtility::formatBytes(memory_get_usage(true)),
            GeneralUtility::formatBytes(memory_get_peak_usage(true))
        ));
        static::$backtraceOffset -= 1;
    }

    /**
     * Print debug information about the given values (arg0, arg1, ... argN)
     *
     * @param $variable
     */
    public static function debug($variable)
    {
        $caller      = static::getCaller();
        $htmlOutput  = php_sapi_name() !== 'cli';
        $colorOutput = isset($_SERVER['TERM']) && strtolower($_SERVER['TERM']) === 'xterm';

        if ($htmlOutput) {
            echo '<pre class="rest-debug"><code>';
        }

        $variables = func_get_args();
        foreach ($variables as $variable) {
            var_dump($variable);
            echo PHP_EOL;
        }
        if ($htmlOutput) {
            echo '</code>';
        }

        // Debug info
        $line = $caller['line'];
        $file = $caller['file'];
        if ($htmlOutput) {
            echo "<span class='rest-debug-path' style='font-size:9px'><a href='file:$file'>see $file($line)</a></span>";
            echo "</pre>";
        } else {
            if ($colorOutput) {
                echo "\033[0;35m" . "$file($line)" . "\033[0m";
            } else {
                echo "($file:$line)";
            }
        }

        if ($htmlOutput) {
            echo '</pre>';
        }
        echo PHP_EOL;
        echo PHP_EOL;
        echo PHP_EOL;
    }

}