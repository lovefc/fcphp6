<?php

namespace FC\Controller;

/**
 * 增删改查
 *
 * @Author: lovefc 
 * @Date: 2019-10-12 14:27:36 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-10-28 16:18:48
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

    // 单独验证一个值
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

    /**
     * 验证变量
     *
     * @param [type] $datas 数组
     * @param [type] $table 表名，用于验证数组中是否有和字段一样的键名
     * @param string $pz 数据库配置，用于连接不同的配置
     * @return void
     */
    final public function checkValues($datas, $table = null, $pz = 'mysql')
    {
        $data = [];
        if (is_array($this->rules)) {
            foreach ($this->rules as $k => $v) {
                $value = isset($datas[$k]) ? $datas[$k] : '';
                try {
                    $data[$k] = Check::regularHandles($v, $value);
                } catch (\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
        }
        if ($table) {
            $data = $this->checkFields($data, $table, $pz);
        }
        return $data;
    }

    // 检测键名是否是字段名
    final public function checkFields($datas, $table, $pz = 'mysql')
    {
        $re = $this->DB::switch($pz)::table($table)->getAllField();
        $res = [];
        if (is_array($re) && is_array($datas)) {
            foreach ($datas as $k => $v) {
                if (in_array($k, $re) && $v != null) {
                    $res[$k] = $v;
                }
            }
        }
        return $res;
    }

    public function error($msg, $e = '')
    {
        die($msg);
    }
}
