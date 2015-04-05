<?php

/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 08.01.15
 * Time: 12:20
 */

use Cundd\PersistentObjectStore\Server\Session\SessionControllerTrait;
use Cundd\PersistentObjectStore\Server\ValueObject\RequestInterface;
use Evenement\EventEmitter;
use React\Socket\ConnectionInterface;
use React\Stream\WritableStreamInterface;
use React\Stream\Util;
use Cundd\PersistentObjectStore\Server\ValueObject\Request;

class Test_Application
{
}

class Test_Application_Controller implements \Cundd\PersistentObjectStore\Server\Controller\ControllerInterface
{
    public function initialize()
    {
    }

    public function setRequest(Request $request)
    {
    }

    public function getRequest()
    {
    }

    public function unsetRequest()
    {
    }

    public function willInvokeAction($action)
    {
    }

    public function didInvokeAction(
        $action,
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
        Request $request,
        \React\Http\Response $response
    ) {
    }


}

class_alias('Test_Application', 'Cundd\\Special\\Application');
class_alias('Test_Application_Controller', 'Cundd\\Test\\Controller\\ApplicationController');
class_alias('Test_Application_Controller', 'Cundd\\TestModule\\Controller\\ApplicationController');

class React_ConnectionStub extends EventEmitter implements ConnectionInterface
{
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

    public function pipe(WritableStreamInterface $dest, array $options = array())
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
 *
 * @package Cundd\PersistentObjectStore\Server\Session
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