<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server\Controller;

use Cundd\Stairtower\Server\Handler\HandlerResultInterface;

/**
 * Interface for classes that describe a Controller response
 */
interface ControllerResultInterface extends HandlerResultInterface
{
    /**
     * Returns the content type of the request
     *
     * @return string
     */
    public function getContentType(): string;

    /**
     * Retrieves all message headers.
     *
     * The keys represent the header name as it will be sent over the wire, and
     * each value is an array of strings associated with the header.
     *
     *     // Represent the headers as a string
     *     foreach ($message->getHeaders() as $name => $values) {
     *         echo $name . ": " . implode(", ", $values);
     *     }
     *
     *     // Emit headers iteratively:
     *     foreach ($message->getHeaders() as $name => $values) {
     *         foreach ($values as $value) {
     *             header(sprintf('%s: %s', $name, $value), false);
     *         }
     *     }
     *
     * While header names are not case-sensitive, getHeaders() will preserve the
     * exact case in which headers were originally specified.
     *
     * @return array Returns an associative array of the message's headers. Each
     *     key MUST be a header name, and each value MUST be an array of strings.
     */
    public function getHeaders(): array;
}
