<?php

// 引入框架
require dirname(__DIR__) . '/FC/Main.php';

try {
    $obj = FC\Obj('FC\Glue\Routes');
    $obj::run();
} catch (\Exception $e) {
    die($e->getMessage());
}


echo FC\GET('a');

echo FC_EOL;
