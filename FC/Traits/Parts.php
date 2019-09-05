<?php

namespace fcphp\traits;

/*
 * 多继承容器
 */

trait Parts
{
    use \fcphp\traits\Parents;//多继承，继承配置

    public static function SetConfigName()
    {
        return 'parts.php';
    }

    //__get()方法用来获取私有属性
    public function __get($name)
    {
        return $this->$name = isset($this->P_Config[$name]) ? $this->GetObj($name) : '';
    }

    //获取实例化
    public function GetObj($name)
    {
        if (is_array($this->P_Config[$name]) && count($this->P_Config[$name]) > 0) {
            $is_static = isset($this->P_Config[$name][1]) ? $this->P_Config[$name][1] : false;
            if ($is_static == true) {
                //直接返回类名
                return $this->P_Config[$name][0];
            }
            $obj = GetObj($this->P_Config[$name][0]);
            return $obj;
        } else {
            //实例化
            return GetObj($this->P_Config[$name]);
        }
    }
}
