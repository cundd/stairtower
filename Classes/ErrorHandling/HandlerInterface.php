<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\ErrorHandling;

/**
 * Interface for Error and Crash Handler
 */
interface HandlerInterface
{
    /**
     * Registers the handler
     */
    public function register();

    /**
     * Perform the actions to handle the problem
     *
     * @param int    $errno
     * @param string $errstr
     * @param string $errfile
     * @param int    $errline
     * @param array  $errcontext
     * @return bool
     */
    public function handle(int $errno, string $errstr, string $errfile = '', int $errline = 0, $errcontext = []);
}