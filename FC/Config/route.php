<?php

/*
 * 路由访问配置
 * @Author: lovefc 
 * @Date: 2019-09-16 15:52:35 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-09-26 14:18:00
 */

return [
    // 默认访问
    'default' => function(){
        echo 'hello world';
    },
    // 正则路由,后面可以跟jquery参数
    '#^view/([0-9]*).html?(.*)$#' => function($a){
        echo $a;
     },
    // 正则路由,后面不可以跟jquery参数
    '#^view2/([0-9]*).html$#' => function($a){
        echo $a;
     }     
];
