<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 15.02.15
 * Time: 12:09
 */

namespace Cundd\PersistentObjectStore\ErrorHandling;


use Cundd\PersistentObjectStore\ErrorException;

/**
 * Error Handler
 *
 * @package Cundd\PersistentObjectStore\ErrorHandling
 */
class ErrorHandler implements HandlerInterface
{
    /**
     * Map of signatures to matching exception class names
     *
     * @var array
     */
    protected $errorMessageSignatures = array(
        // Exception class name => Signature
        'Cundd\\PersistentObjectStore\\Exception\\StringTransformationException' => 'Object of class %s could not be converted to string'
    );

    /**
     * Registers the handler
     */
    public function register()
    {
        set_error_handler(array($this, 'handle'));
    }

    /**
     * Perform the actions to handle the problem
     *
     * @param int    $errno
     * @param string $errstr
     * @param string $errfile
     * @param int    $errline
     * @param array  $errcontext
     * @return bool
     * @throws ErrorException
     */
    public function handle($errno, $errstr, $errfile = '', $errline = 0, $errcontext = array())
    {
        if (E_RECOVERABLE_ERROR === $errno) {
            foreach ($this->errorMessageSignatures as $exceptionClassName => $errorMessageSignature) {
                $match = sscanf($errstr, $errorMessageSignature);
                if ($match && isset($match[0]) && $match[0] !== null) {
                    throw new $exceptionClassName($errstr, 0, $errno, $errfile, $errline);
                }
            }
            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
        }
        return false;
    }
}