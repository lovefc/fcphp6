<?php

/*
 * 路由访问配置
 * @Author: lovefc 
 * @Date: 2019-09-16 15:52:35 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-09-16 15:52:56
 */

return [
    'default' => function($a){
        echo "hello-{$a}";
    },
    
    '1' => function(){
       echo '2222';
    },
];
