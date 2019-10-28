<?php

namespace FC\Controller;

/**
 * 增删改查
 *
 * @Author: lovefc 
 * @Date: 2019-10-12 14:27:36 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-10-28 14:56:27
 */

abstract class BaseController
{
    use \FC\Traits\Parts;

    // 规则
    public $rules = [];

    // 增加规则
    final public function addRule($name, $array = '')
    {
        if (is_array($name)) {
            foreach ($name as $k => $v) {
                $this->rules[$k] = $v;
            }
        }
        if ($name && $array) {
            $this->rules[$name] = $array;
        }
    }
    // 检测值是否存在
    final public function checkValue($key, $value)
    {
        $value = $value;
        $kes = $this->rules[$key];
        try {
            $status = Check::regularHandles($kes, $value);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        return $status;
    }

    // 检测值是否存在
    final public function checkValues($datas)
    {
        $data = [];
        if (is_array($this->rules)) {
            foreach ($this->rules as $k => $v) {
                $value = isset($datas[$k]) ? $datas[$k] : '';
                $data[$k] = Check::regularHandles($v, $value);
            }
        }
        return $data;
    }

    public function error($msg, $e = '')
    {
        die($msg);
    }
}
