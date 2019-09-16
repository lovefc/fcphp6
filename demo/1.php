<?php
// 定义编码
header("Content-type:text/html; charset=utf-8");

header("Content-type:text/json; charset=utf-8");

header("Server: custom-server", true);

header("Fc: 6.0");

header("X-Powered-By: FC/6.0");

header("Server: node/8.8.8");

//print_r(headers_list());
$browser = get_browser();

print_r($browser);


?>
