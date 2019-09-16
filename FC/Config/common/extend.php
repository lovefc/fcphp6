<?php
return array(
	 
    //测试邮箱
    'MAIL' => array(
        'Charset' => 'UTF-8',
        'Port' => 587,
        'SMTPSecure' =>'ssl',
        'SMTPAuth' => true,
        'Host' => 'smtp.qq.com',
        'Username' => 'bycodes@qq.com',//support@bycodes.net
        'Password' => 'ontuihvicziwecab',
        'Form' => 'bycodes@qq.com',
        'Formname' => 'support',
        'Replyto' => 'support@bycodes.net',
        'Replyname' => 'support'
     ),	 

	 
    //图片缩略,验证码设置
    'IMAGE' => array(
	    //远程图片本地缓存目录
        'urlpath' => PATH.'/upload/url',
		
		//图片缩略图本地缓存目录
        'simgpath'  => PATH.'/upload/simg',
		
		//是否每次重新生成缩略图
        'imgcache' => '1',
		
		//字体路径
        'fonturl' => '',
		
		//验证码宽度
        'width' =>100,
		
        //验证码高度		
        'height'=>30,
		
        //验证码个数		
        'nums'   => 4,
		
        //随机数,中文需要字体支持	
        'code' => 'qwertyupasdfhkzxcvbnm23456789',
    ),	 
);
