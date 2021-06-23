<?php

/*
 * 路由访问配置
 * @Author: lovefc 
 * @Date: 2019-09-16 15:52:35 
 * @Last Modified by: lovefc
 * @Last Modified time: 2021-06-23 09:23:17
 */

return [

    // 默认访问,这里有参数$a，可以在get中用/?a=123来改变
    'default' => function ($a = 'world') {
        echo "hello {$a}";
    },
    
    // 这里都是绑定类库,访问方式都是/captcha/方法名/
    'captcha' => '\Main\Controller\captcha',

    'curl' => '\Main\Controller\curl',  

    'redis' => '\Main\Controller\redis',

    'db' => '\Main\Controller\db',  

    'view' => '\Main\Controller\view',

    'order' => '\Main\Controller\order',    
    
    'curd'  => '\Main\Controller\curd',

    // 正则路由,后面可以跟jquery参数
    '#^cs.html(.*)$#' => ['\Main\Controller\curd','index'],

    // 正则路由,后面可以跟jquery参数
    '#^html/([0-9]*).html(.*)$#' => function($a){
        echo $a;
     }, 
    '#^images/([0-9]*).jpg(.*)$#' => function ($a) {
        echo $a;
    },

];
