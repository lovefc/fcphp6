<?php
//要加载的类和文件
return array(

    "psr-4" => array(
        //如果是数组，那么将是 路径 , 后缀 , 优先级
        //'demo'=>array(路径,’php',1)
        'controller'   => PATH.'/application/controller',
    ),
    
    "psr-0" => array(),
    
    "files" => array(
	    PATH.'/common/function/common.php'
	),
);
