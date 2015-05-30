<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 30.05.15
 * Time: 11:27
 */
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/../../../autoload.php')) {
    require_once __DIR__ . '/../../../autoload.php';
} else {
    echo 'Can not find composer autoload file. Aborting';
    exit(1);
}

$argv = array(
    'get' => $_GET,
    'post' => $_POST,
    'cookie' => $_COOKIE,
    'files' => $_FILES,
    'server' => $_SERVER,
);
$routerBootstrap = new Cundd\PersistentObjectStore\Bootstrap\Router($argv);
$routerBootstrap->execute();
