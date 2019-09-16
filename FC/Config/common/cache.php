<?php
/**
 * 缓存配置
 */
return array(

    //默认配置
    'default' => array(

        //缓存目录，如果是memcache，redis情况下，则要设置成iP地址
        'Path' => PATH.'/cache',
        //'Path'   =>'127.0.0.1',
        
        //memcache或者redis的端口
        'Port' => '',
        
        //缓存方式，有三种情况，memcache,redis，file
        'Mode' => 'file',
        
        //针对文件缓存的过期时间,redis和memcache设置这个选项无效
        'Time' => 999999
        
    ),
	
);
