<?php

/*
 * 路由访问配置
 * @Author: lovefc 
 * @Date: 2019-09-16 15:52:35 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-09-24 13:27:47
 */

return [
    'default' => function($a){
        echo "hello-{$a}";
    },
    
    '1' => function(){
       echo '2222';
    },
    '2' => ['\\Main\cs','index'], 
    '3' => ['\\Main\cs','index2'],     
];
