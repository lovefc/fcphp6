<?php

/*
 * SQLITE 数据库配置
 * @Author: lovefc 
 * @Date: 2019-10-10 08:36:07 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-10-10 08:38:20
 */

return [

    //默认链接数据库
    'default' => [
 
        // 数据库路径
        'DbName' => PATH['NOW'] . '/Sql/ceshi.db',

        // 数据库用户名
        'DbUser' => 'root',

        // 数据库密码
        'DbPwd' => '',

        // 长链接
        'Attr' => false,

        // 数据库编码
        'Charset' => 'utf8',

        // 数据库表前缀
        'Prefix' => '',

    ],

];
