<?php

/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 08.01.15
 * Time: 12:20
 */

use Evenement\EventEmitter;
use React\Socket\ConnectionInterface;
use React\Stream\WritableStreamInterface;
use React\Stream\Util;


class Test_Application
{
}

class Test_Application_Controller implements \Cundd\PersistentObjectStore\Server\Controller\ControllerInterface
{
    public function initialize()
    {
    }

    public function setRequestInfo(\Cundd\PersistentObjectStore\Server\ValueObject\RequestInfo $requestInfo)
    {
    }

    public function getRequestInfo()
    {
    }

    public function unsetRequestInfo()
    {
    }

    public function getRequest()
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
        \Cundd\PersistentObjectStore\Server\ValueObject\RequestInfo $requestInfo,
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
