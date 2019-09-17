<?php

namespace FC\Glue;

use FC\Route;

/*
 * @Author: lovefc 
 * @Date: 2019-09-16 15:05:57 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-09-17 08:55:08
 */

class Routes extends Route
{
    //继承配置
    use \FC\traits\Parents;

    //初始设置
    public function init()
    {
        self::$routeval = $this->P_Config;
    }

    //错误消息
    public function error($msg)
    {
        \ErrorShow($msg);
    }
}
