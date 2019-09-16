<?php

// 引入框架
require dirname(__DIR__) . '/FC/Main.php';


function Main()
{
    try {

        $obj = new FC\Glue\Routes;

        $obj::run();
    } catch (\Exception $e) {

        die($e->getMessage());
    }
}

Main();

$Stime = $_SERVER['REQUEST_TIME_FLOAT'];

$Etime = microtime(true);

echo FC_EOL;

echo round($Etime - $Stime, 5);
