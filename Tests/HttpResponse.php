<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Tests;

use Throwable;

class HttpResponse implements \ArrayAccess
{
    /**
     * @var int
     */
    private $status;

    /**
     * @var array
     */
    private $parsedBody;

    /**
     * @var string
     */
    private $body;

    /**
     * @var array
     */
    private $headers;

    /**
     * @var Throwable
     */
    private $error;

    /**
     * HttpResponse constructor.
     *
     * @param int            $status
     * @param array|null     $parsedBody
     * @param string|bool    $body
     * @param array          $headers
     * @param Throwable|null $error
     */
    public function __construct(int $status, ?array $parsedBody, $body, array $headers, ?Throwable $error = null)
    {
        $this->status = $status;
        $this->parsedBody = $parsedBody;
        $this->body = $body;
        $this->headers = $headers;
        $this->error = $error;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return array
     */
    public function getParsedBody(): ?array
    {
        return $this->parsedBody;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return (string)$this->body;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function isSuccess(): bool
    {
        return 0 < $this->status && $this->status < 400;
    }

    /**
     * @return Throwable
     */
    public function getError(): ?Throwable
    {
        return $this->error;
    }


    public function offsetExists($offset)
    {
        return isset($this->parsedBody[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->parsedBody[$offset];
    }

    public function offsetSet($offset, $value)
    {
        throw new \LogicException(__METHOD__ . ' not implemented');
    }

    public function offsetUnset($offset)
    {
        throw new \LogicException(__METHOD__ . ' not implemented');
    }


}
