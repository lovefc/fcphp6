<?php

namespace FC\Glue;

use FC\Cache\Cache as Caches;

/*
 * 缓存类库
 * @Author: lovefc 
 * @Date: 2019-09-24 10:58:20
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-10-09 15:41:20
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
    
    // 返回redis句柄
    public function R(){
        $this->ReadConf('redis');
        return $this->obj();
    }
    
    // 返回files句柄
    public function F(){
        $this->ReadConf('files');
        return $this->obj();
    }    
    
    // 返回files句柄
    public function M(){
        $this->ReadConf('memcache');
        return $this->obj();
    }       
    
    // 错误消息
    public function error($msg)
    {
        \FC\Log::Show($msg);
    }
}