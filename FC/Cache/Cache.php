<?php

namespace fcphp\extend\cache;

/*
 * 数据缓存类
 * 用于缓存数据
 * memcache or redis or file
 * author:lovefc
 * time:2016/5/20
 */

class Cache
{

    public $Path; //地址
    public $Port; //端口
    public $Mode; //方式
    public $Time; //文件缓存过期时间
    public $IsMd5 = false; //缓存文件的时候,保存的文件名是否md5加密
    public $Obj = array(); //类的接口
    public $Ext = '.cache'; //缓存的文件后缀
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

    /**
     * 引入一个缓存文件
     */
    public function incfile($key)
    {
        if ($this->IsMd5 == true) {
            $key = md5($key);
        }
        $path = $this->obj() . '/' . $key . $this->Ext;
        if (is_file($path)) {
            require($path);
        }
    }

    /*
     * 设置一个参数值
     * @param $key 参数名
     * @param $value 参数值
     * 设置过期时间,file形式下无效
     */

    public function set($key, $value, $expire = 60)
    {
        $expire = ($expire == 0) ? $this->Time : $expire;
        if ($this->Mode == 'file') {
            return $this->file_set($key, $value, $expire);
        } else {
            if ($this->Mode == 'memcache') {
                return $this->obj()->set($key, $value, 0, $expire);
            } else {
                return $this->obj()->set($key, $value, $expire);
            }
        }
    }

    /*
     * 获取一个参数
     */

    public function get($key)
    {
        if (!$this->has($key)) {
            return false;
        }
        if ($this->Mode == 'file') {
            return $this->file_get($key);
        } else {
            return $this->obj()->get($key);
        }
    }

    /*
     * 删除一个参数
     */

    public function del($key)
    {
        if ($this->Mode == 'file') {
            return $this->file_dele($key);
        } else {
            if ($this->check_key($key)) {
                $this->obj()->delete($key);
            }
        }
    }

    /*
     * 判断一个参数是否已经过期
     */

    public function has($key)
    {
        clearstatcache();
        if ($this->Mode == 'file') {
            if ($this->IsMd5 == true) {
                $key = md5($key);
            }
            $path = $this->obj() . '/' . $key . $this->Ext;
            if (is_file($path) && (time() - filemtime($path)) <= $this->Time) {
                return true;
            } else {
                return false;
            }
        } else {
            $obj = $this->obj();
            if ($this->Mode == 'redis') {
                if (method_exists($obj, 'exists')) {
                    $data = $obj->exists($key);
                } else {
                    $data = $obj->has($key);
                }
            } else {
                $data = $obj->get($key);
            }
            return $data;
        }
    }

    /*
     * 文件形式的参数设置
     */

    public function file_set($key, $value, $time = 0)
    {
        if ($this->IsMd5 == true) {
            $key = md5($key);
        }
        $path = $this->obj() . '/' . $key . $this->Ext;
        $this->create($path, true);
        if (file_put_contents($path, $value)) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * 文件形式的参数读取
     */

    public function file_get($key)
    {
        if ($this->IsMd5 == true) {
            $key = md5($key);
        }
        $path = $this->obj() . '/' . $key . $this->Ext;
        if (is_file($path)) {
            return file_get_contents($path);
        } else {
            return false;
        }
    }

    /*
     * 文件形式的参数删除
     */

    public function file_dele($key)
    {
        if ($this->IsMd5 == true) {
            $key = md5($key);
        }
        $path = $this->obj() . '/' . $key . $this->Ext;
        if (is_file($path)) {
            unlink($path);
            return true;
        }
        return false;
    }

    /**
     * 创建一个文件或者目录
     * @param $dir 目录名或者文件名
     * @param $file 如果是文件，则设为true
     * @param $mode 文件的权限
     * @return false|true
     */
    public function create($dir, $file = false, $mode = 0777)
    {
        $path = str_replace("\\", "/", $dir);
        if ($file) {
            if (is_file($path)) {
                return true;
            }
            $temp_arr = explode('/', $path);
            array_pop($temp_arr);
            $file = $path;
            $path = implode('/', $temp_arr);
        }
        if (!is_dir($path)) {
            @mkdir($path, $mode, true);
        } else {
            @chmod($path, $mode);
        }
        if ($file) {
            $fh = @fopen($file, 'a');
            if ($fh) {
                fclose($fh);
                return true;
            }
        }
        if (is_dir($path)) {
            return true;
        }
        return false;
    }

    /*
     * 打印错误
     */

    public function error($msg)
    {
        die($msg);
    }
}
