<?php

/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 08.01.15
 * Time: 12:20
 */
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
}

class_alias('Test_Application', 'Cundd\\Special\\Application');
class_alias('Test_Application_Controller', 'Cundd\\Test\\Controller\\ApplicationController');
class_alias('Test_Application_Controller', 'Cundd\\TestModule\\Controller\\ApplicationController');