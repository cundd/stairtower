<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Tests\Fixtures;

use Evenement\EventEmitter;
use React\Socket\ConnectionInterface;
use React\Stream\Util;
use React\Stream\WritableStreamInterface;

class ReactConnectionStub extends EventEmitter implements ConnectionInterface
{
    private $data = '';

    public function getLocalAddress()
    {
    }

    public function isReadable()
    {
        return true;
    }

    public function isWritable()
    {
        return true;
    }

    public function pause()
    {
    }

    public function resume()
    {
    }

    public function pipe(WritableStreamInterface $stream, array $options = [])
    {
        Util::pipe($this, $stream, $options);

        return $stream;
    }

    public function write($data)
    {
        $this->data .= $data;

        return true;
    }

    public function end($data = null)
    {
    }

    public function close()
    {
    }

    public function getData()
    {
        return $this->data;
    }

    public function getRemoteAddress()
    {
        return '127.0.0.1';
    }
}
