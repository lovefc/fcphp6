<?php

namespace FC\Cache;

use FC\File;

/*
 * 文件缓存类
 * @Author: lovefc
 * @Date: 2019-10-03 00:24:20
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-10-03 00:45:26
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

    public function connect($Path, $IsMd5 = false, $Ext = '.cache', $Time = 60)
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
     * 引入一个缓存文件.
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
     * @param $key 参数名
     * @param $value 参数值
     * 设置过期时间,file形式下无效
     */

    public function set($key, $value, $expire = 60)
    {
        $expire = (0 == $expire) ? $this->Time : $expire;
        if ('file' == $this->Mode) {
            return $this->file_set($key, $value, $expire);
        } else {
            if ('memcache' == $this->Mode) {
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
        if ('file' == $this->Mode) {
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
        if ('file' == $this->Mode) {
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
        if ('file' == $this->Mode) {
            if (true == $this->IsMd5) {
                $key = md5($key);
            }
            $path = $this->obj().'/'.$key.$this->Ext;
            if (is_file($path) && (time() - filemtime($path)) <= $this->Time) {
                return true;
            } else {
                return false;
            }
        } else {
            $obj = $this->obj();
            if ('redis' == $this->Mode) {
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

    /**
     * 创建一个文件或者目录.
     *
     * @param $dir 目录名或者文件名
     * @param $file 如果是文件，则设为true
     * @param $mode 文件的权限
     *
     * @return false|true
     */
    public function create($dir, $file = false, $mode = 0777)
    {
        $path = str_replace('\\', '/', $dir);
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
            mkdir($path, $mode, true);
        } else {
            chmod($path, $mode);
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
}
