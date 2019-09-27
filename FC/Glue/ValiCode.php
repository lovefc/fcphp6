<?php

namespace FC\Glue;

use FC\Http\ValiCode as Code;

/*
 * @Author: lovefc 
 * @Date: 2019-09-27 15:06:40
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-09-27 17:37:22
 */

class ValiCode extends Code
{
    // 继承配置
    use \FC\traits\Parents;

    // 初始设置
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
