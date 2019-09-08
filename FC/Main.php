<?php

/**
 * 框架基本入口
 * @Author: lovefc 
 * @Date: 2019-09-09 01:07:17 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-09-09 01:08:51
 */

 // 判断运行版本
version_compare(PHP_VERSION, '7.0.0', '<=') && exit("fcphp框架只能运行在php7版本或以上的环境中,敬请见谅!\n");

// 定义时区
!defined('TIMEZONE') ? date_default_timezone_set('PRC') : date_default_timezone_set(TIMEZONE);

// 定义编码
!defined('CHARSET') ? header("Content-type:text/html; charset=utf-8") : header('Content-type: text/html; charset=' . CHARSET);

// 检测是否定义fastcgi_finish_request
if (!function_exists("fastcgi_finish_request")) {
    function fastcgi_finish_request()
    {
    }
}

// 判断get_magic_quotes_gpc
if (function_exists('get_magic_quotes_gpc')) {
    define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
} else {
    define('MAGIC_QUOTES_GPC', 0);
}

// 判断web运行的绝对目录，在ng和ap中，这个值是不同的
$ROOT_PATH = $_SERVER['CONTEXT_DOCUMENT_ROOT'] ?? '' || $_SERVER['HOME'] ?? '';

// 是否为AJAX请求 
// jquery.js发起的请求，会包含HTTP_X_REQUESTED_WITH字段，如果自定义请求，需要这样定义请求来判断
/**
 * var xmlhttp = new XMLHttpRequest();
 * xmlhttp.open("GET","test.php",true);
 * xmlhttp.setRequestHeader("X-Requested-With", "XMLHttpRequest");
 * xmlhttp.send();
*/
$IS_AJAX = ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) ? true : false;

// pathinfo下对于PHP_SELF的兼容，避免出现不必要的值和安全问题,如果要获取本页面地址，推荐使用$_SERVER['SCRIPT_NAME']
isset($_SERVER['PATH_INFO']) ? $_SERVER['PHP_SELF'] = $_SERVER['SCRIPT_NAME'] : '';

// 虽然有$_SERVER全局变量,但不可太过于依赖它，这里用于兼容判断取值
define('SERVER', [
     // 服务器ip
     'IP'   =>  $_SERVER['SERVER_ADDR'] ?? '',
     // 端口
     'PORT' =>  $_SERVER['SERVER_PORT'] ?? '',
     // 域名地址
     'HOST' => $_SERVER['HTTP_HOST'] ?? '',
     //请求开始的时间戳
     'TIME' => $_SERVER['REQUEST_TIME'] ?? '',
     // 协议头，https或者http
     'SCHEMA' => $_SERVER['REQUEST_SCHEME'] ?? '',
     // 请求方式
     'METHOD' => $_SERVER['REQUEST_METHOD'] ?? '',
     // 是否为ajax请求
     'AJAX' => $IS_AJAX,
     // 当前执行脚本的绝对路径
     'NOW_PATH'   => isset($_SERVER['SCRIPT_FILENAME']) ? dirname($_SERVER['SCRIPT_FILENAME']) : '', // 当前路径
     // web运行的绝对目录
     'ROOT_PATH'  => $ROOT_PATH,
     // 请求开始的时间戳
     'TIME_FLOAT' => $_SERVER['REQUEST_TIME_FLOAT'] ?? '',
     // 是否为cli模式
     'CLI' => (PHP_SAPI === 'cli') ? true : false,
     // 查询字符串
     'QUERY_STRING' => $_SERVER['QUERY_STRING'] ?? '',
     
]);