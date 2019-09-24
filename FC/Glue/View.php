<?php
namespace FC\Glue;
use FC\View\Eztpl;

class View extends Eztpl
{
    use \FC\Traits\Parents;//继承

    
    //初始设置
    public function _init()
    {
        $this->ReadConf('default');//默认配置，此选项用于多配置选择
        (!empty($this->P_Config['TPL_REPLACE'])) ? $this->binds($this->P_Config['TPL_REPLACE']) : '';
    }
    
    //错误消息
    public function error($msg)
    {
        \FC\Log::Show($msg);
    }
}
