<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 30.05.15
 * Time: 11:27
 */
require_once __DIR__.'/_autoload.php';

$argv = array(
    'get' => $_GET,
    'post' => $_POST,
    'cookie' => $_COOKIE,
    'files' => $_FILES,
    'server' => $_SERVER,
);
$routerBootstrap = new Cundd\PersistentObjectStore\Bootstrap\Router($argv);
$routerBootstrap->execute();
