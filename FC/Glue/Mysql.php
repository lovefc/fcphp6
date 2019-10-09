<?php

namespace FC\Glue;

use FC\Db\Mysql as MY;

/*
 * mysql数据库
 * @Author: lovefc 
 * @Date: 2019-10-09 15:38:02 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-10-09 15:41:31
 */

class Mysql extends MY
{
    use \fcphp\traits\Parents;

    // 初始化操作
    public function _init()
    {
        // 默认配置，此选项用于多配置选择
        $this->ReadConf('default');
    }

    // 错误消息
    public function error($msg)
    {
        \FC\Log::Show($msg);
    }
}
