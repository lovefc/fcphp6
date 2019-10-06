<?php
/*
 * 多部件继承配置(简易容器)
 * 这里配置之后，继承之后，直接可以使用类变量
 * 例如 $this->IMG
 * 
 * @Author: lovefc 
 * @Date: 2019-09-22 17:41:41 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-10-06 17:50:37
 */
return [
    // Curl类
    'CURL'     => 'FC\Http\Curl',
    
    // 缓存类
    'CACHE'    => 'FC\Glue\Cache',

    // 文件缓存类
    'FCACHE'   => ['FC\Glue\Cache', 'files'],  

    // redis类
    'REDIS'    => ['FC\Glue\Cache','redis'],

    // memcache缓存类
    'MEMCACHE'    => ['FC\Glue\Cache','memcache'],   

    // 视图类
    'VIEW'     => 'FC\Glue\View',

    // Session类
    'SESSION'  => 'FC\Glue\Session',

    // Cookies类
    'COOKIES'  => 'FC\Http\Cookies',
    
    // 验证码类
    'CAPTCHA'  => 'FC\Glue\Captcha',
];
