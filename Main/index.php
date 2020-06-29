<?php

// 报错显示
define('DEBUG', true);

// 定义错误日志
define('LOG_DIR',__DIR__.'/Log');

// 引入框架
require dirname(__DIR__) . '/FC/Main.php';

// 框架初始化
\FC\Main::init();

// 运行框架
\FC\Main::run();

//echo FC_EOL;

//echo round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 5);
