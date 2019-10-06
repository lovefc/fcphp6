<?php

namespace FC\Cache;

use FC\Container;

/*
 * 数据缓存类
 * memcache or redis or file
 * @Author: lovefc
 * @Date: 2019-10-03 00:24:47
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-10-06 16:40:06
 */

class Cache
{
    // 地址
    public $Path; 
    // 端口
    public $Port; 
    // 方式
    public $Mode; 
    // 类的接口
    public $Obj = array(); 
    // 缓存文件的时候,保存的文件名是否md5加密
    public $IsMd5 = true;
    // 缓存的文件后缀
    public $Ext = '.cache';
    // 文件缓存过期时间
    public $Time = '60';
    // 类型
    public $ConfigType;


    /**
     * 魔术方法，用来创建方法
     *
     * @param [type] $method
     * @param [type] $args
     * @return void
     */
    
    public function __call($method, $args)
    {
        if (('setMode' == $method) && isset($args[0])) {
            $this->ConfigType  =  $this->Mode = $args[0];
            $obj = $this->obj();
            return $obj;
        }
    }
    

    /*
     * 判断缓存方式
     */

    public function obj()
    {
        if (isset($this->Obj[$this->ConfigType])) {
            return $this->Obj[$this->ConfigType];
        }
        switch ($this->Mode) {
            case 'memcache':
                $obj = $this->memcache();
                break;
            case 'redis':
                $obj = $this->redis();
                break;
            case 'file':
                $obj = $this->files();
                break;
        }
        if (isset($obj)) {
            $this->Obj[$this->ConfigType] = $obj;
            return $obj;
        } else {
            $this->error('缓存方式配置出错');
        }
        return false;
    }

    /*
     * memcache
     */

    public function memcache()
    {
        if (class_exists('Memcache', false)) {
            $obj = new \FC\Cache\Memcache();
        } else {
            $obj = new Memcached();
        }
        $obj->connect($this->Path, $this->Port) or $this->error('Could not connect as memcache');

        return $obj;
    }

    /*
     * redis
     */

    public function redis()
    {
        $obj = null;
        if (class_exists('Redis', false)) {
            $obj = new \FC\Cache\Redis();
        } else {
            $obj = new Redis();
        }
        $obj->connect($this->Path, $this->Port) or $this->error('Could not connect as redis');
        return $obj;
    }

    /*
     * files
     */

    public function files()
    {
        $obj = new \FC\Cache\Files();
        $obj->connect($this->Path, $this->IsMd5, $this->Ext, $this->Time);
        return $obj;
    }

    /*
     * 打印错误
     */

    public function error($msg)
    {
        die($msg);
    }
}
