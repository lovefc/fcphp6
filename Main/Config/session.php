<?php

/*
 * Session 配置
 * @Author: lovefc 
 * @Date: 2019-09-24 12:32:45 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-09-25 15:05:39
 */

return [
    'default' => [
        // Session前缀
        'prefix' => 'fc_',
        // Session的名称
        'name' => 'FCSESSION',
        // 存储路径
        'save_path' => 'tcp://127.0.0.1:6379',
        // 存储方式
        'save_handler' => 'redis',
    ],
];
