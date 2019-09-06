<?php
namespace fcphp\start;
use fcphp\extend\cache\Cache;

class cacheStart extends Cache
{
    use \fcphp\traits\Parents;//继承
    
    // 初始设置
    public function init()
    {
        $this->ctype('default');//默认配置，此选项用于多配置选择
    }
    
    //错误消息
    public function error($msg)
    {
        \ErrorShow($msg);
    }
}
