<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\ErrorHandling;

use Cundd\PersistentObjectStore\ErrorException;

/**
 * Error Handler
 */
class ErrorHandler implements HandlerInterface
{
    /**
     * Map of signatures to matching exception class names
     *
     * @var array
     */
    protected $errorMessageSignatures = [
        // Exception class name => Signature
        'Object of class %s could not be converted to string'          => 'Cundd\\PersistentObjectStore\\Exception\\StringTransformationException',
        'Argument %d passed to %s must be an instance of %s, %s given' => 'Cundd\\PersistentObjectStore\\Exception\\InvalidArgumentError',
    ];

    /**
     * Registers the handler
     */
    public function register()
    {
        set_error_handler([$this, 'handle']);
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
    public function handle(int $errno, string $errstr, string $errfile = '', int $errline = 0, $errcontext = [])
    {
        if (E_RECOVERABLE_ERROR === $errno) {
            foreach ($this->errorMessageSignatures as $errorMessageSignature => $exceptionClassName) {
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