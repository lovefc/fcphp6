<?php

namespace FC\Glue;

/*
 * 数据库链接
 * @Author: lovefc 
 * @Date: 2019-10-09 15:38:02 
 * @Last Modified by: lovefc
 * @Last Modified time: 2021-06-23 14:09:26
 */

class Mysql extends \FC\Db\Mysql
{
    use \FC\Traits\Parents;

    // 初始设置
    public function _init()
    {
        // 默认配置，此选项用于多配置选择
        $this->ReadConf('default');
    }
}
