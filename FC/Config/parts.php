<?php
/*
 * 多部件继承配置(简易容器)
 * 这里配置之后，继承之后，直接可以使用类变量
 * 例如 $this->IMG
 */
return array(

    //如果是数组，三个参数，1为类名，2为是否为静态类
    'DB'      => 'fcphp\start\mysqlStart',
    
    'DB_MY'   => 'fcphp\start\mysqlStart',
    
    'DB_ITE'  => 'fcphp\start\sqliteStart',
    
    'TPL'     => 'fcphp\start\tplStart',
    
    'CACHE'   => 'fcphp\start\cacheStart',
    
    'SESSION' => 'fcphp\extend\Session',
    
    'COOKIES' => 'fcphp\extend\Cookies',
    
    'FILES'   => 'fcphp\extend\Files',
    
    'IMG'     => 'fcphp\start\imgStart',

    'GITHUB'  => 'extend\start\hubStart',
    
    'CODING'  => 'extend\start\codStart',
    
    'OSCHINA' => 'extend\start\oscStart',
  
    'QC'      => 'extend\start\qcStart',
  
	'EMAIL'    => 'extend\start\mailStart'
    
);
