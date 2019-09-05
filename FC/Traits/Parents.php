<?php

namespace fcphp\traits;

/**
 * 缓存一些常用的数据
 */
class CacheVars
{
    //保存例实例在此属性中
    private static $_instance;
    public $P_Configs;
    public $P_PublicConfig;
    public $P_ArrayConfig;

    //构造函数声明为private,防止直接创建对象
    private function __construct()
    { }

    //单例方法
    public static function singleton()
    {
        if (!isset(self::$_instance)) {
            $c = __CLASS__;
            self::$_instance = new $c;
        }
        return self::$_instance;
    }
}

/*
 * 继承父类
 */

trait Parents
{
    //配置数组
    public $P_Config;

    //保存当前的类名
    public $P_ClassName;

    public static $P_CacheVars;


    //访问配置选项
    public $P_ConfigType = false;

    //注册的类变量
    public $P_RegVar = array();

    /*
     * 构造器 开始执行
     */
    public function __construct()
    {
        self::$P_CacheVars = CacheVars::singleton(); //获取单例


        self::$P_CacheVars->P_Configs = self::$P_CacheVars->P_PublicConfig;

        $this->P_ClassName = get_class($this); //获取类名

        if (isset(self::$P_CacheVars->P_Configs[$this->P_ClassName])) {
            $this->P_Config = self::$P_CacheVars->P_Configs[$this->P_ClassName];
        } else {
            $this->P_DefaultArrayConfig();
            $this->P_Config = self::$P_CacheVars->P_Configs[$this->P_ClassName] = self::P_receive($this->P_ClassName);
        }
        //if (count($this->P_Config) != 0) {
        if (method_exists($this, 'init')) {
            $this->init();
        }
        if (empty($this->P_ConfigType)) {
            $this->P_RegVar = array_keys($this->P_Config);
        } else {
            $this->P_RegVar = array_keys($this->P_Config[$this->P_ConfigType]);
        }
        $this->ObjStart();
        if (method_exists($this, 'start')) {
            $this->start();
        }
        //}
    }

    /*
     * 析构方法,清除变量
     */
    public function __destruct()
    {
        unset($this->P_Config);
    }

    //__get()方法用来获取私有属性
    public function __get($name)
    {
        $this->P_RegVar[] = $name;
        if (empty($this->P_ConfigType)) {
            return $this->$name = isset($this->P_Config[$name]) ? $this->P_Config[$name] : '';
        } else {
            return $this->$name = isset($this->P_Config[$this->P_ConfigType][$name]) ? $this->P_Config[$this->P_ConfigType][$name] : '';
        }
    }

    //初始化
    public function ObjStart()
    {
        foreach ($this->P_RegVar as $value) {
            unset($this->$value);
        }
        return $this;
    }


    //设置访问的配置
    public function ctype($type)
    {
        if (isset($type)) {
            $this->P_ConfigType = $type;
        }
        $this->ObjStart(); //初始化
        return $this;
    }

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
            if (empty($this->P_ConfigType)) {
                $this->P_Config[$property] = $args[0];
            } else {
                $this->P_Config[$this->P_ConfigType][$property] = $args[0];
            }
        }
        if ($perfix == "get") {
            $this->$property = $args[0];
            if (empty($this->P_ConfigType)) {
                return $this->P_Config[$property];
            } else {
                return $this->P_Config[$this->P_ConfigType][$property];
            }
        }
        return $this;
    }

    //获取配置，并关联
    final public static function P_GetConfig($file, $file2)
    {
        $config = self::P_GetConfigFile($file);
        $config2 = self::P_GetConfigFile($file2);
        return array_replace_recursive($config, $config2); //组合配置
    }

    //读取配置目录以及工作目录里的配置文件
    final public static function P_ReadConfigFile($conf)
    {
        $file = CONFIG_DIR . '/' . $conf;
        if (is_file($file)) {
            $file2 = CONFIG_APP_DIR . '/' . $conf;
            $config = self::P_GetConfig($file, $file2);
            return $config;
        }
        return array();
    }

    //读取一个配置文件
    final public static function P_GetConfigFile($file, $ckey = '')
    {
        if (is_file($file)) {
            $re = include($file);
            if (is_array($re)) {
                self::$P_CacheVars->P_PublicConfig[$ckey] = $re;
                return $re;
            } else {
                return array();
            }
        } else {
            self::$P_CacheVars->P_PublicConfig[$ckey] = self::P_ReadConfigFile($file);
            return self::$P_CacheVars->P_PublicConfig[$ckey];
        }
        return array();
    }

    //读取配置,$conf代表类名
    final public static function P_receive($conf)
    {
        if (!$conf) {
            return false;
        }
        $ckey = substr(md5($conf), 3, 6);
        if (isset(self::$P_CacheVars->P_PublicConfig[$ckey])) {
            return self::$P_CacheVars->P_PublicConfig[$ckey];
        }
        $jian = $arr = array(); //初始化变量
        if (array_key_exists($conf, self::$P_CacheVars->P_ArrayConfig)) {
            $conf = self::$P_CacheVars->P_ArrayConfig[$conf];
        } else {
            //在数组中检查
            if (method_exists($conf, 'SetConfigName')) {
                $conf = $conf::SetConfigName();
            }
        }
        //在方法中检查
        if ($conf) {
            if (is_array($conf)) {
                return $conf;
            }

            //是数组直接返回
            if (strpos($conf, '::')) {
                $arr = explode('::', $conf); //分割字符串
                $conf = $arr[0]; //取得第一个值
                if ($conf) {
                    array_shift($arr);
                    $jian = $arr; //获取键名
                } else {
                    array_shift($arr);
                    $conf = $arr; //键值
                }
            }
            $config = self::P_GetConfigFile($conf, $ckey);
            $config2 = self::P_ImpArray($config, $jian);
            $re = $config2 ? $config2 : $config;

            return $re;
        }
        return array();
    }

    //配置关联
    final public function P_DefaultArrayConfig()
    {
        if (self::$P_CacheVars->P_ArrayConfig == null) {
            self::$P_CacheVars->P_ArrayConfig = $this->P_ReadConfigFile('config.php');
        }
    }

    /*
     * 获取到多维数组的值
     * @param $config 数组
     * @param $array 键名，多个
     */
    final public static function P_ImpArray($config, $array)
    {
        if (!is_array($config)) {
            return false;
        }
        if (is_array($array) && count($array) > 0) {
            foreach ($array as $value) {
                $config = isset($config[$value]) ? $config[$value] : null;
            }
            return $config;
        }
        return $config;
    }
}
