<?php

// 引入框架
require dirname(__DIR__) . '/FC/Main.php';


// 开启报错
//define('DEBUG', true);

try {
    $obj = FC\Obj('FC\Glue\Route');
    $obj::run();
} catch (\Exception $e) {
    die($e->getMessage());
}

echo md5(uniqid(random_int(0,10000), true));

echo FC_EOL;

echo round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 5);
