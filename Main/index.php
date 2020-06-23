<?php

// 报错显示
define('DEBUG', true);

// 引入框架
require dirname(__DIR__) . '/FC/Main.php';

// 定义错误日志
define('LOG_DIR',__DIR__.'/Log');

try {
    $obj = FC\obj('FC\Glue\Route');
    $obj::run();
} catch (\Exception $e) {
    \FC\Log::Show($e->getMessage());
}

//echo FC_EOL;

//echo round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 5);
