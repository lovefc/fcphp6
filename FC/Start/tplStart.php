<?php
namespace fcphp\start;
use fcphp\extend\Eztpl;

class tplStart extends Eztpl
{
    use \fcphp\traits\Parents;//继承

    
    //初始设置
    public function init()
    {
        $this->ctype('default');//默认配置，此选项用于多配置选择
        (!empty($this->P_Config['TPL_REPLACE'])) ? $this->bind($this->P_Config['TPL_REPLACE']) : '';
    }
    
    //错误消息
    public function error($msg)
    {
        \ErrorShow($msg);
    }
}
