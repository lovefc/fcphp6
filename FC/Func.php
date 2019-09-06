<?php

// 过滤二进制
function cleanHex($input){
    $clean = preg_replace("![][xX]([A-Fa-f0-9]{1,3})!", "",$input);
    return $clean;
}
        
// 过滤参数的长度
foreach ($_GET as $k => $v){
    $_GET[$k] = substr($v,0,50); 
}