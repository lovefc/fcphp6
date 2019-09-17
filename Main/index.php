<?php

// 引入框架
require dirname(__DIR__) . '/FC/Main.php';

try {
    $obj = FC\S('FC\Glue\Routes');
    $obj::run();
} catch (\Exception $e) {
    die($e->getMessage());
}

echo FC_EOL;

echo round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 5);
