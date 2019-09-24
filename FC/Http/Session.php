<?php

namespace FC\Http;

/**
 * 简单的session封装
 * 
 * @Author: lovefc 
 * @Date: 2019-09-24 10:14:06 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-09-24 17:36:48
 */

class Session
{
    // Session前缀
    public $_prefix;
    // Session的名称
    public $_name = 'FCSESSION';
    // 在读取完毕会话数据之后马上关闭会话存储文件
    public $cache_limite = 'private';
    // SessionID在客户端Cookie储存的时间，默认是0，代表浏览器一关闭SessionID就作废
    public $cookie_lifetime = 3600;
    // Cookies存储路径
    public $cookie_path = '/';
    // Cookies 域名
    public $cookie_domain = '';
    // 是否将httpOnly标志添加到cookie中，这使得浏览器脚本语言(如JavaScript)无法访问该标志。
    public $cookie_httponly = false;
    // 在读取完会话数据之后， 立即关闭会话存储文件，不做任何修改
    public $read_and_close  = false;
    // 存储路径
    public $save_path = '';
    // 存储方式
    public $save_handler = 'files';
    // 定义“垃圾收集”过程启动的概率
    public $gc_probability = 1;
    // 垃圾收集，运行概率
    public $gc_divisor = 100;
    // Session在服务端存储的时间
    public $gc_maxlifetime = 1440;

    /**
     * 初始化
     */
    public function __construct()
    {
        $this->init();
    }
    
    /**
     * 初始化属性赋值
     */
    public function init()
    {
        if (!isset($_SESSION)) {
            session_start([
                'cache_limiter' => $this->cache_limiter,
                //'read_and_close' => $this->read_and_close,
                'cookie_path' => $this->cookie_path,
                'cookie_domain' => $this->cookie_domain,
                'cookie_httponly' => $this->cookie_httponly,
                'cookie_lifetime' => $this->cookie_lifetime,
                'save_path' => $this->save_path,
                'save_handler' => $this->save_handler,
                'name' => $this->_name,
                'gc_probability' => $this->gc_probability,
                'gc_divisor' => $this->gc_divisor,
                'gc_maxlifetime' => $this->gc_maxlifetime
               
            ]);
        }
    }

    /**
     * 设置session的前缀
     * @param $prefix
     * @return object
     */
    public function prefix($prefix)
    {
        if (is_string($prefix) && $prefix != '') {
            $this->_prefix = $prefix;
        }
        return $this;
    }

    /**
     * 设置一个session的值
     * @param $name 键名
     * @param $value 内容
     * @return null
     */
    public function set($name, $value)
    {
        $name    = empty($this->_prefix) ? $name : $this->_prefix . $name;
        $_SESSION[$name] = $value;
        // 提前写入到文件,也可用session_write_close
        session_commit();
    }

    /**
     * 获取一个session的值
     * @param $name 键名
     * @return false|string
     */
    public function get($name)
    {
        $name    = empty($this->_prefix) ? $name : $this->_prefix . $name;
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        } else {
            return false;
        }
    }

    /**
     * 检测一个session的值
     * @param $name 键名
     * @return true|false
     */
    public function has($name)
    {
        $name    = empty($this->_prefix) ? $name : $this->_prefix . $name;
        if (isset($_SESSION[$name])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 删除一个session的值
     * @param $name 键名
     * @return null
     */
    public function del($name)
    {
        $name    = empty($this->_prefix) ? $name : $this->_prefix . $name;
        unset($_SESSION[$name]);
    }

    /**
     * 清空session
     */
    public function clear()
    {
        $_SESSION = array();
        session_destroy();
    }
}
