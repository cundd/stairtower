<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server\ValueObject;


use Evenement\EventEmitter;
use React\Socket\ConnectionInterface;
use React\Stream\WritableStreamInterface;

class WriteOnlyConnection extends EventEmitter implements ConnectionInterface
{
    public $stream;
    protected $writable = true;
    protected $closing = false;

    protected $data = '';
    protected $lastError = [
        'number'  => 0,
        'message' => '',
        'file'    => '',
        'line'    => 0,
    ];

    public function __construct()
    {
        $this->stream = fopen('php://output', 'w');
    }


    public function getRemoteAddress()
    {
        echo "getRemoteAddress()" . PHP_EOL;
    }

    public function isReadable()
    {
        echo "isReadable()" . PHP_EOL;
    }

    public function pause()
    {
        echo "pause()" . PHP_EOL;
    }

    public function resume()
    {
        echo "resume()" . PHP_EOL;
    }

    public function pipe(WritableStreamInterface $dest, array $options = [])
    {
        echo "pipe()" . PHP_EOL;
    }


    public function isWritable()
    {
        echo "isWritable()" . PHP_EOL;
    }

    public function write($data)
    {
        if (!$this->writable) {
            return false;
        }

        return $this->doWrite($data);
    }

    public function close()
    {
        if (!$this->writable && !$this->closing) {
            return;
        }

        $this->closing = false;

        $this->writable = false;
        $this->data = '';

        $this->emit('end', [$this]);
        $this->emit('close', [$this]);
        $this->removeAllListeners();

        $this->handleClose();
    }

    public function end($data = null)
    {
        if (!$this->writable) {
            return;
        }

        $this->closing = true;

        $this->writable = false;
        $this->doWrite($data);
    }

    public function handleClose()
    {
        if (is_resource($this->stream)) {
            fclose($this->stream);
        }
    }

    /**
     * Write the data to the output
     *
     * @param $data
     * @return bool
     */
    protected function doWrite($data)
    {
        if ($this->isHeader($data)) {
            $this->sendHeader($this->splitHeader($data));
        } else {
            fwrite(STDERR, $data);
            fwrite(STDERR, __FILE__ . '@' . __LINE__ . "\n");
            $this->handleWrite($data);
            //$sent = 1;
            ////fwrite(fopen('php://output', 'w'), __FILE__ . '@' . __LINE__ . "\n");
            //
            //
            ////stream_set_blocking($this->stream, 0);
            //
            ////echo $data;
            ////$sent = fwrite($this->stream, var_export($data, true));
            //
            //if (0 === $sent && feof($this->stream)) {
            //    //$this->emit('error', array(new \RuntimeException('Tried to write to closed stream.'), $this));
            //    throw new \RuntimeException('Tried to write to closed stream.');
            //}
        }

        //fwrite()$data;
        //var_dump($data);
        return true;
    }

    protected function handleWrite()
    {
        if (!is_resource($this->stream)) {
            throw new \RuntimeException('Tried to write to invalid stream.');
        }

        //set_error_handler(array($this, 'errorHandler'));

        //$stream = fopen('php://output', 'w');
        //$sent = fwrite($stream, $this->data);
        //var_dump($sent);
        //fflush($stream);
        //echo 'fadsfads';

        $sent = fwrite($this->stream, $this->data);
        fflush($this->stream);

        //restore_error_handler();

        if (false === $sent) {
            throw new \ErrorException(
                $this->lastError['message'],
                0,
                $this->lastError['number'],
                $this->lastError['file'],
                $this->lastError['line']
            );
        }

        if (0 === $sent && feof($this->stream)) {
            throw new \RuntimeException('Tried to write to closed stream.');
        }

        //$len = strlen($this->data);
        //if ($len >= $this->softLimit && $len - $sent < $this->softLimit) {
        //    $this->emit('drain', [$this]);
        //}

        $this->data = (string)substr($this->data, $sent);

        //if (0 === strlen($this->data)) {
        //    $this->loop->removeWriteStream($this->stream);
        //    $this->listening = false;
        //
        //    $this->emit('full-drain', [$this]);
        //}
    }

    private function errorHandler($errno, $errstr, $errfile, $errline)
    {
        $this->lastError['number'] = $errno;
        $this->lastError['message'] = $errstr;
        $this->lastError['file'] = $errfile;
        $this->lastError['line'] = $errline;
    }

    /**
     * Send the headers
     *
     * @param array $headers
     */
    protected function sendHeader(array $headers)
    {
        array_walk($headers, 'header');
    }

    /**
     * Returns if the given data is the header data
     *
     * @param $data
     * @return bool
     */
    protected function isHeader($data)
    {
        return (is_string($data) && substr($data, 0, 9) === 'HTTP/1.1 ');
    }

    /**
     * Split the header into its lines
     *
     * @param $data
     * @return array
     */
    protected function splitHeader($data)
    {
        return explode("\n", str_replace("\r\n", "\n", $data));
    }
}
