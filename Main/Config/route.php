<?php

/*
 * 路由访问配置
 * @Author: lovefc 
 * @Date: 2019-09-16 15:52:35 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-10-25 14:49:48
 */

return [

    // 默认访问,这里有参数$a，可以在get中用/?a=123来改变
    'default' => function ($a = 'world') {
        echo "hello {$a}";
    },
    
    // 这里都是绑定类库,访问方式都是/captcha/方法名/
    'captcha' => '\Main\Test\captcha',

    'curl' => '\Main\Test\curl',  

    'redis' => '\Main\Test\redis',

    'db' => '\Main\Test\db',  

    'view' => '\Main\Test\view',

    'order' => '\Main\Test\order',    
    
    'curd'  => '\Main\Test\curd',

    // 正则路由,后面可以跟jquery参数
    '#^cs.html(.*)$#' => ['\Main\Test\curd','index'],

    // 正则路由,后面可以跟jquery参数
    '#^html/([0-9]*).html(.*)$#' => function($a){
        echo $a;
     }, 
    '#^images/([0-9]*).jpg(.*)$#' => function ($a) {
        echo $a;
    },

];
