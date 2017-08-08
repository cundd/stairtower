<?php
declare(strict_types=1);

namespace Cundd\Stairtower\ErrorHandling;

use Cundd\Stairtower\ErrorException;
use Cundd\Stairtower\Exception\InvalidArgumentError;
use Cundd\Stairtower\Exception\StringTransformationException;

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
        'Object of class %s could not be converted to string'          => StringTransformationException::class,
        'Argument %d passed to %s must be an instance of %s, %s given' => InvalidArgumentError::class,
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
     * @param int    $code
     * @param string $message
     * @param string $file
     * @param int    $line
     * @param array  $context
     * @return bool
     * @throws ErrorException
     */
    public function handle(int $code, string $message, string $file = '', int $line = 0, $context = [])
    {
        if (E_RECOVERABLE_ERROR === $code) {
            foreach ($this->errorMessageSignatures as $errorMessageSignature => $exceptionClassName) {
                $match = sscanf($message, $errorMessageSignature);
                if ($match && isset($match[0]) && $match[0] !== null) {
                    throw new $exceptionClassName($message, 0, $code, $file, $line);
                }
            }
            throw new ErrorException($message, 0, $code, $file, $line);
        }

        return false;
    }
}