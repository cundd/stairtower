<?php
declare(strict_types=1);

require_once __DIR__ . '/_autoload.php';

$argv = [
    'get'    => $_GET,
    'post'   => $_POST,
    'cookie' => $_COOKIE,
    'files'  => $_FILES,
    'server' => $_SERVER,
];
$routerBootstrap = new Cundd\PersistentObjectStore\Bootstrap\Router($argv);
$routerBootstrap->execute();
