<?php
declare(strict_types=1);


use Cundd\PersistentObjectStore\Server\Session\SessionControllerTrait;
use Cundd\PersistentObjectStore\Server\ValueObject\RequestInterface;
use Evenement\EventEmitter;
use React\Socket\ConnectionInterface;
use React\Stream\WritableStreamInterface;
use React\Stream\Util;

class Test_Application
{
}

class Test_Application_Controller implements \Cundd\PersistentObjectStore\Server\Controller\ControllerInterface
{
    public function initialize(): void
    {
    }

    public function setRequest(RequestInterface $request
    ): \Cundd\PersistentObjectStore\Server\Controller\ControllerInterface {
        return $this;
    }

    public function getRequest(): RequestInterface
    {
        return null;
    }

    public function unsetRequest(): void
    {
    }

    public function willInvokeAction(string $action): void
    {
    }


    public function didInvokeAction(
        string $action,
        \Cundd\PersistentObjectStore\Server\Controller\ControllerResultInterface $result
    ) {
    }

    public function getMyMethodAction()
    {
    }

    public function postMyMethodAction()
    {
    }

    public function deleteMyMethodAction()
    {
    }

    public function putMyMethodAction()
    {
    }

    public function headMyMethodAction()
    {
    }

    public function processRequest(
        RequestInterface $request,
        WritableStreamInterface $response
    ) {
    }


}

class_alias('Test_Application', 'Cundd\\Special\\Application');
class_alias('Test_Application_Controller', 'Cundd\\Test\\Controller\\ApplicationController');
class_alias('Test_Application_Controller', 'Cundd\\TestModule\\Controller\\ApplicationController');

class React_ConnectionStub extends EventEmitter implements ConnectionInterface
{
    public function getLocalAddress()
    {
    }

    private $data = '';

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

    public function pipe(WritableStreamInterface $dest, array $options = [])
    {
        Util::pipe($this, $dest, $options);

        return $dest;
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

/**
 * Dummy Session Controller
 */
class Test_Session_Controller
{
    use SessionControllerTrait;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param RequestInterface $request
     * @return $this
     */
    public function setRequest($request)
    {
        $this->request = $request;

        return $this;
    }
}