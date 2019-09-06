<?php
namespace fcphp\start;
use fcphp\extend\MVC;

class mvcStart extends MVC
{
    use \fcphp\traits\Parents;
    
    //错误消息
    public function error($msg)
    {
        \ErrorShow($msg);
    }    
}