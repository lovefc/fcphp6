<?php

namespace FC\Cache;

use FC\Cache\{Files, Redis, memcache};


 /*
 * 数据缓存类
 * memcache or redis or file
 * @Author: lovefc 
 * @Date: 2019-10-03 00:24:47 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-10-03 00:35:23
 */

class Cache
{

    public $Path; //地址
    public $Port; //端口
    public $Mode; //方式
    public $Obj = array(); //类的接口
    public $ConfigType;

    //魔术方法，用来创建方法
    public function __call($method, $args)
    {
        $perfix = substr($method, 0, 3);
        $property = substr($method, 3);
        if (empty($perfix) || empty($property)) {
            return $this;
        }
        if ($perfix == "set") {
            $this->$property = $args[0];
        }
        return $this;
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
                $obj = $this->Path;
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
            $obj = new \fcphp\extend\cache\Memcache;
        } else {
            $obj = new Memcached;
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
            $obj = new \fcphp\extend\cache\Redis;
        } else {
            $obj = new Redis;
        }
        $obj->connect($this->Path, $this->Port) or $this->error('Could not connect as redis');
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
