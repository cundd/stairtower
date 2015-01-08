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

class Test_Application_Controller
{
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