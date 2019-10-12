<?php

/*
 * 路由访问配置
 * @Author: lovefc 
 * @Date: 2019-09-16 15:52:35 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-10-12 17:12:37
 */

return [

    'default' => function ($a = 'world') {
        echo "hello {$a}";
    },
    
    'captcha' => '\Main\Test\captcha',

    'curl' => '\Main\Test\curl',  

    'redis' => '\Main\Test\redis',

    'db' => '\Main\Test\db',      

    // 正则路由,后面可以跟jquery参数
    '#^html/([0-9]*).html(.*)$#' => function($a){
        echo $a;
     }, 
     
    '#^images/([0-9]*).jpg(.*)$#' => function ($a) {
        echo $a;
    },

];
