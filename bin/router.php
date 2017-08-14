<?php
declare(strict_types=1);

use Cundd\Stairtower\Bootstrap\Router;
use Cundd\Stairtower\Server\Exception\BootException;

require_once __DIR__ . '/_autoload.php';

if (php_sapi_name() === 'cli-server') {
    $arguments = [
        'get'    => $_GET,
        'post'   => $_POST,
        'cookie' => $_COOKIE,
        'server' => $_SERVER,
        'files'  => $_FILES,
    ];
} elseif (php_sapi_name() === 'cli') {
    $arguments = [
        'get'    => [],
        'post'   => [],
        'cookie' => [],
        'server' => [],
        'files'  => [],
    ];
    if (isset($argv[1])) {
        parse_str($argv[1], $arguments['get']);
    }
    if (isset($argv[2])) {
        parse_str($argv[2], $arguments['post']);
    }
    if (isset($argv[3])) {
        parse_str($argv[3], $arguments['cookie']);
    }
    if (isset($argv[4])) {
        parse_str($argv[4], $arguments['server']);
    }
    if (isset($argv[5])) {
        parse_str($argv[5], $arguments['files']);
    }
} else {
    echo 'Unsupported SAPI ' . php_sapi_name() . PHP_EOL;
    exit(BootException::EXIT_CODE);
}

(new Router())->execute($arguments);
