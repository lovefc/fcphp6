<?php

namespace FC\Cache;

use FC\File;

/*
 * 文件缓存类
 * @Author: lovefc
 * @Date: 2019-10-03 00:24:20
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-10-03 15:02:26
 */

class Files
{
    // 文件保存地址
    public $Path;
    // 缓存文件的时候,保存的文件名是否md5加密
    public $IsMd5 = false;
    // 缓存的文件后缀
    public $Ext = '.cache';
    // 文件缓存过期时间
    public $Time;

    /**
     * 缓存配置
     *
     * @param [type] $Path
     * @param bool   $IsMd5
     * @param string $Ext
     * @param int    $Time
     *
     * @return void
     */
    public function connect($Path = '', $IsMd5 = false, $Ext = '.cache', $Time = 60)
    {
        $this->Path = $Path;
        if (!is_dir($path)) {
            File::create($path);
        }
        $this->IsMd5 = $IsMd5;
        $this->Ext = $Ext;
        $this->Time = $Time;
    }

    /**
     * 引入一个缓存文件
     *
     * @param [type] $key
     * @return void
     */
    public function incfile($key)
    {
        if (true == $this->IsMd5) {
            $key = md5($key);
        }
        $path = $this->obj().'/'.$key.$this->Ext;
        if (is_file($path)) {
            require $path;
        }
    }

    /*
     * 设置一个参数值
     * 设置过期时间,file形式下无效
     * @param $key 参数名
     * @param $value 参数值
     * @return void
     */

    public function set($key, $value, $expire = 60)
    {
        $expire = (0 == $expire) ? $this->Time : $expire;
        return $this->file_set($key, $value, $expire);
    }

    /**
     * 获取一个参数
     *
     * @param [type] $key
     * @return void
     */
    public function get($key)
    {
        if (!$this->has($key)) {
            return false;
        }
        return $this->file_get($key);

    }

    /*
     * 删除一个参数
     */

    public function del($key)
    {
        return $this->file_dele($key);
    }

    /*
     * 判断一个参数是否已经过期
     */

    public function has($key)
    {
        clearstatcache();
        if (true == $this->IsMd5) {
            $key = md5($key);
        }
        $path = $this->obj().'/'.$key.$this->Ext;
        if (is_file($path) && (time() - filemtime($path)) <= $this->Time) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * 文件形式的参数设置
     */

    public function file_set($key, $value, $time = 0)
    {
        if (true == $this->IsMd5) {
            $key = md5($key);
        }
        $path = $this->obj().'/'.$key.$this->Ext;
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
        if (true == $this->IsMd5) {
            $key = md5($key);
        }
        $path = $this->obj().'/'.$key.$this->Ext;
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
        if (true == $this->IsMd5) {
            $key = md5($key);
        }
        $path = $this->obj().'/'.$key.$this->Ext;
        if (is_file($path)) {
            unlink($path);

            return true;
        }

        return false;
    }
}
