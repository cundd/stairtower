<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 15.02.15
 * Time: 12:09
 */

namespace Cundd\PersistentObjectStore\ErrorHandling;

/**
 * Interface for Error and Crash Handler
 *
 * @package Cundd\PersistentObjectStore\ErrorHandling
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
    public function handle($errno, $errstr, $errfile = '', $errline = 0, $errcontext = array());
}
