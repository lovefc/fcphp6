<?php
namespace fcphp\start;
use fcphp\core\Route;

class routeStart extends Route
{
    //继承配置
    use \fcphp\traits\Parents;
    
    //初始设置
    public function init()
    {
        self::$routeval = $this->P_Config;
        self::$rule = self::P_receive('rule.php');
    }
    
    //错误消息
    public function error($msg)
    {
        \ErrorShow($msg);
    }    
}
