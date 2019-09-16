<?php

// 引入框架
require dirname(__DIR__) . '/FC/Main.php';


try {

    $obj = new FC\Glue\Routes;
    
    $obj::run();    

    /*
    // 关闭错误
    FC\Route::$showError = false;
    
    FC\Route::set('default',function(){
        echo '123';
    });
    
    FC\Route::set('demo2','\cs',['b'=>'英文']);

    FC\Route::set('demo',['\cs','demo'],['b'=>'数字']);
    
    FC\Route::run(); 
    */
    
} catch (\Exception $e) {
    
    die($e->getMessage());
}



$Stime = $_SERVER['REQUEST_TIME_FLOAT'];

$Etime = microtime(true);
echo '<br />';

echo round($Etime - $Stime, 5);
