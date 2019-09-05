<?php

require __DIR__.'/WebServer.php';

require __DIR__.'/Execs.php';

require __DIR__.'/Route.php';

$obj = new swoole\WebServer();

$obj->start();

//$obj->pre($_SERVER);