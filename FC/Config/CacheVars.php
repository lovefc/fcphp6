<?php

namespace FC\Config;

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