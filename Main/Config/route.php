<?php

/*
 * 路由访问配置
 * @Author: lovefc 
 * @Date: 2019-09-16 15:52:35 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-09-30 08:17:30
 */

return [

    'default' => function ($a) {
        echo "hello-{$a}";
    },
    '1' => function () {
        echo '2222';
    },

    '2' => ['\\Main\cs', 'index'],

    '3' => ['\\Main\cs', 'index2'],

    '4' => ['\\Main\cs', 'index3'],

    '#^images/([0-9]*).jpg(.*)$#' => function ($a) {
        echo $a;
    },

];
