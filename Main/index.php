<?php

// 引入框架
require dirname(__DIR__) . '/FC/Main.php';

// 关闭报错
define('DEBUG', true);

try {
    $obj = FC\Obj('FC\Glue\Route');
    $obj::run();
} catch (\Exception $e) {
    \FC\Log::Show($e->getMessage());
}

echo FC_EOL;

echo round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 5);
