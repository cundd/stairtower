<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 30.05.15
 * Time: 14:11
 */

namespace Cundd\PersistentObjectStore\Server\ValueObject;


use Evenement\EventEmitter;
use React\Http\ResponseCodes;
use React\Stream\WritableStreamInterface;

/**
 * Implementation of a Response without an attached connection
 *
 * @package Cundd\PersistentObjectStore\Server\ValueObject
 */
class SimpleResponse extends EventEmitter implements WritableStreamInterface
{
    protected $closed = false;
    protected $writable = true;
    protected $conn;
    protected $headWritten = false;
    protected $chunkedEncoding = false;

    /**
     * Returns if the response is writable
     *
     * @return bool
     */
    public function isWritable()
    {
        return $this->writable;
    }

    /**
     * Write the head
     *
     * @param int   $status
     * @param array $headers
     * @throws \Exception
     */
    public function writeHead($status = 200, array $headers = array())
    {
        if ($this->headWritten) {
            throw new \Exception('Response head has already been written.');
        }

        if (isset($headers['Content-Length'])) {
            $this->chunkedEncoding = false;
        }

        $headers = array_merge(
            array('X-Powered-By' => 'React/alpha'),
            $headers
        );
        if ($this->chunkedEncoding) {
            $headers['Transfer-Encoding'] = 'chunked';
        }

        $this->sendHeader($status, $headers);

        $this->headWritten = true;
    }

    /**
     * Write the data
     *
     * @param $data
     * @return bool
     * @throws \Exception
     */
    public function write($data)
    {
        if (!$this->headWritten) {
            throw new \Exception('Response head has not yet been written.');
        }

        if ($this->chunkedEncoding) {
            $len     = strlen($data);
            $chunk   = dechex($len) . "\r\n" . $data . "\r\n";
            $flushed = $this->doWrite($chunk);
        } else {
            $flushed = $this->doWrite($data);
        }

        return $flushed;
    }

    /**
     * End the request
     *
     * @param mixed $data
     * @throws \Exception
     */
    public function end($data = null)
    {
        if (null !== $data) {
            $this->write($data);
        }

        if ($this->chunkedEncoding) {
            $this->doWrite("0\r\n\r\n");
        }

        $this->emit('end');
        $this->removeAllListeners();
    }


    /**
     * Close the response stream
     */
    public function close()
    {
        if ($this->closed) {
            return;
        }

        $this->closed = true;

        $this->writable = false;
        $this->emit('close');
        $this->removeAllListeners();
    }

    /**
     * @throws \Exception
     */
    public function writeContinue()
    {
        if ($this->headWritten) {
            throw new \Exception('Response head has already been written.');
        }

        $this->doWrite("HTTP/1.1 100 Continue\r\n");
    }

    /**
     * Write the data to the output
     *
     * @param $data
     * @return bool
     */
    protected function doWrite($data)
    {
        $stream = fopen('php://output', 'w');
        stream_set_blocking($stream, 0);
        $sent = fwrite($stream, $data);

        if (0 === $sent && feof($stream)) {
            throw new \RuntimeException('Tried to write to closed stream.');
        }
        return true;
    }

    /**
     * Send the headers
     *
     * @param int $status
     * @param array $headers
     */
    protected function sendHeader($status, array $headers)
    {
        $status = (int)$status;
        $text   = isset(ResponseCodes::$statusTexts[$status]) ? ResponseCodes::$statusTexts[$status] : '';
        header("HTTP/1.1 $status $text");

        foreach ($headers as $name => $value) {
            $name = str_replace(array("\r", "\n"), '', $name);

            foreach ((array)$value as $val) {
                $val = str_replace(array("\r", "\n"), '', $val);

                header("$name: $val");
            }
        }
    }
}
