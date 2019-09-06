<?php
namespace fcphp\start;
use fcphp\extend\db\Sqlite;
 
class sqliteStart extends Sqlite
{
    use \fcphp\traits\Parents;//继承配置
    
    //初始化
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
