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
     * @param int    $code
     * @param string $message
     * @param string $file
     * @param int    $line
     * @param array  $context
     * @return bool
     */
    public function handle(int $code, string $message, string $file = '', int $line = 0, $context = []);
}
