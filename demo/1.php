<?php
// 定义编码
header("Content-type:text/html; charset=utf-8");
header("Server: custom-server", true);
header("Fc: 6.0");
header("X-Powered-By: FC/6.0");
header("Server: node/8.8.8");



class cs{
  // 测试
  public static function hello(){
     $lasterror = error_get_last();
     ob_clean();
     print_r($lasterror);
  }
}

$cs = new cs;

define('ERROR',[
   'cs',
   'hello'
]);

register_shutdown_function(ERROR); 

echo new a;