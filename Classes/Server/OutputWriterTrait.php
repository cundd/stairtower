<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server;

trait OutputWriterTrait
{
    /**
     * Outputs the given value for information
     *
     * @param string $format
     * @param array  ...$vars
     */
    public static function writeln(string $format, ...$vars)
    {
        static::formatAndWrite($format, ...$vars);
        static::formatAndWrite(PHP_EOL);
    }

    /**
     * Outputs the given value for information
     *
     * @param string $format
     * @param array  ...$vars
     */
    public static function write(string $format, ...$vars)
    {
        static::formatAndWrite($format, ...$vars);
    }

    /**
     * Outputs the given value for information
     *
     * @param string $format
     * @param array  ...$vars
     */
    public static function formatAndWrite(string $format, ...$vars)
    {
        if (!empty($vars)) {
            $format = vsprintf($format, $vars);
        }
        fwrite(STDOUT, $format);
    }
}