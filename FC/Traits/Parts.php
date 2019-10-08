<?php

namespace FC\Traits;

/*
 * 多继承容器
 * @Author: lovefc
 * @Date: 2019-09-20 10:17:40
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-10-06 10:24:15
 */

trait Parts
{
    // 多继承，继承配置
    use \FC\Traits\Parents;

    public static function SetConfigName()
    {
        return 'parts.php';
    }

    // __get()方法用来获取私有属性
    public function __get($name)
    {
        return $this->$name = isset($this->P_Config[$name]) ? $this->_GetObj($name) : '';
    }

    // 获取实例化
    public function _GetObj($name)
    {
        if (is_array($this->P_Config[$name]) && count($this->P_Config[$name]) > 0) {
            $method = (bool) isset($this->P_Config[$name][1]) ? $this->P_Config[$name][1] : false;
            $obj = \FC\obj($this->P_Config[$name][0]);
            if ($method) {
                try {
                    return $obj->$method();
                }catch (\Exception $e) {
                    die($e->getMessage());
                }
            }

            return $obj;
        } else {
            // 实例化
            return \FC\obj($this->P_Config[$name]);
        }
    }
}
