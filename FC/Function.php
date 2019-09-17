<?php

namespace FC;

/*
 * 框架公用函数库
 * @Author: lovefc 
 * @Date: 2016/9/09 13:29:34 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-09-17 10:26:52
 */


/**
 * 获取类的单例
 *
 * @param [type] $class
 * @param string $dir
 * @param string $mode
 * @return void
 */
function S($class, $mode = 'cache')
{
    if (!$class) {
        return false;
    }
    $class = ltrim($class, '\\');
    static $fcobj = array();
    if (isset($fcobj[$class]) && $mode != 'notcache') {
        return $fcobj[$class];
    }
    if (class_exists($class)) {
        switch ($mode) {
            case 'cache':
                $fcobj[$class] = new $class;
                break;
            case  'notcache':
                return new $class;
                break;
            default:
                $fcobj[$class] = new $class;
        }
        return $fcobj[$class];
    }
    return false;
}
