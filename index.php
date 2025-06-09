<?php

use classes\Core;

spl_autoload_register(function ($class) {
    $path = str_replace('\\', '/', $class) . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
});

$core = Core::getInstance();
$core->init();
$core->run();
$core->done();