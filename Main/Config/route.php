<?php

/*
 * 路由访问配置
 * @Author: lovefc 
 * @Date: 2019-09-16 15:52:35 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-10-12 09:41:37
 */

return [

    'default' => function ($a = 'world') {
        echo "hello {$a}";
    },

    'redis' => '\Main\Test\redis',

    '2' => ['\\Main\cs', 'index'],

    '3' => ['\\Main\cs', 'index2'],

    '4' => ['\\Main\cs', 'index3'],

    // 正则路由,后面可以跟jquery参数
    '#^html/([0-9]*).html(.*)$#' => function($a){
        echo $a;
     }, 
     
    '#^images/([0-9]*).jpg(.*)$#' => function ($a) {
        echo $a;
    },

];
