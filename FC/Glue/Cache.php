<?php

namespace FC\Glue;

use FC\Cache\Cache as Caches;

/*
 * @Author: lovefc 
 * @Date: 2019-09-24 10:58:20
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-10-06 16:38:05
 */

class Cache extends Caches
{
    // 继承配置
    use \FC\traits\Parents;
    
    // 初始设置
    public function _init()
    {
        // 默认配置，此选项用于多配置选择
        $this->ReadConf('files');
    }

    // 错误消息
    public function error($msg)
    {
        \FC\Log::Show($msg);
    }
}