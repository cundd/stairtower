<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server;

use Cundd\Stairtower\Formatter\FormatterInterface;
use Cundd\Stairtower\Server\Handler\HandlerResultInterface;
use Cundd\Stairtower\Server\ValueObject\RequestInterface;
use Exception;
use Psr\Http\Message\ResponseInterface;

interface ResponseBuilderInterface
{
    /**
     * Build a Response instance for the given Request Result
     *
     * @param HandlerResultInterface $result
     * @param RequestInterface       $request
     * @return ResponseInterface
     */
    public function buildResponseForResult(?HandlerResultInterface $result, RequestInterface $request): ResponseInterface;

    /**
     * Handles the given exception
     *
     * @param Exception        $error
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function buildErrorResponse(Exception $error, RequestInterface $request): ResponseInterface;

    /**
     * Returns the formatter for the given request
     *
     * @param RequestInterface $request
     * @return FormatterInterface
     * @throws Exception if no matching Formatter could be found
     */
    public function getFormatterForRequest(RequestInterface $request): FormatterInterface;

    /**
     * Returns the status code that best describes the given error
     *
     * @param Exception $error
     * @return int
     */
    public function getStatusCodeForException($error):int;
}
