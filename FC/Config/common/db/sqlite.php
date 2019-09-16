<?php
return array(

    //默认链接数据库
    'default' => array(
        //主机地址
        'Host' => '127.0.0.1',
        
        //数据库名
        'DbName' => PATH.'/'.md5('lovefc').'.db',
        
        //数据库用户名
        'DbUser' => 'root',
        
        //数据库密码
        'DbPwd' => '123456',
        
        //长链接
        'Attr' => false,
        
        //数据库编码
        'Charset' => 'utf8',
        
        //数据库表前缀
        'Prefix' => 'fc_',
        
    ),
    
     //默认链接数据库
    'demo' => array(
        //主机地址
        'Host' => '127.0.0.1',
        
        //数据库名
        'DbName' => PATH.'/demo.db',
        
        //数据库用户名
        'DbUser' => 'root',
        
        //数据库密码
        'DbPwd' => '123456',
        
        //长链接
        'Attr' => false,
        
        //数据库编码
        'Charset' => 'utf8',
        
        //数据库表前缀
        'Prefix' => 'fc_',
        
    ),   
);
