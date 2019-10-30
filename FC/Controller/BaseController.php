<?php

namespace FC\Controller;

use FC\Json;

/**
 * 父类控制器，基础的控制器
 *
 * @Author: lovefc 
 * @Date: 2019-10-12 14:27:36 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-10-30 09:45:06
 */

abstract class BaseController
{
    // 规则
    public $rules = [];
    // 非空
    public $not_empty = [];
    // 数据库操作句柄
    public $db;
    // 是否允许清空数据
    public $clean = false;
    // 保留不被删除的值
    public $keep  = [];
    // 主键名称
    public $primary = '';
    // 表名
    public $table = '';
    // 跨域访问控制
    public $cross = false;

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

    // 初始化设置
    public function _start()
    {
        if ($this->cross === true) {
            \FC\setOrigin(false, 'POST,GET,OPTIONS,PUT,DELETE', true);
        }
    }

    // 清空数据库
    public function clean()
    {
        if ($this->clean === true) {
            $table = $this->table;
            $this->db::cleanTable($table);
        }
    }

    // 删除操作
    public function delete($id, $field = '', $pz = 'mysql')
    {
        $ids = (array) $id;
        $table = $this->table;
        // 获取主键
        if (!$this->primary) {
            $this->primary = $this->DB::switch($pz)::getPK($table);
        }
        $res = [];
        foreach ($ids as $k => $kid) {
            if (in_array($kid, $this->keep)) {
                continue;
            }
            if (!$field) {
                $where[$this->primary] = $kid;
            } else {
                $where[$field] = $kid;
            }
            $res[$k] = $this->db::name($table)->where($where)->del();
        }
        $str = implode($res, '');
        if (strpos($str, '0') === false) {
            return true;
        }
        return false;
    }

    /**
     * 检测非空
     *
     * @param [type] $msg
     * @return void
     */
    public function error($msg)
    {
        $code = 1;
        $msg = "{$msg}值错误";
        Json::error($code, $msg);
    }

    /**
     * 检测非空
     *
     * @param [type] $datas
     * @return void
     */
    final public function notEmpty($datas)
    {
        foreach ($this->not_empty as $v) {
            if (!isset($datas[$v]) || $datas[$v] == null) {
                $this->error($v);
            }
        }
    }
    /**
     * 单独验证一个值
     *
     * @param [type] $key 值名称
     * @param [type] $value 值
     * @return bool
     */
    final public function checkValue($key, $value)
    {
        $value = $value;
        $kes = $this->rules[$key];
        $status = Check::regularHandles($kes, $value);
        return $status;
    }

    /**
     * 验证过滤数组
     *
     * @param [type] $datas 数组
     * @param [type] $table 表名，用于验证数组中是否有和字段一样的键名
     * @param string $pz 数据库配置，用于连接不同的配置
     * @return array
     */
    final public function checkValues($datas, $table = null, $pz = 'mysql')
    {

        $data = [];
        if (is_array($datas)) {
            foreach ($datas as $k => $v) {
                if (isset($this->rules[$k])) {
                    $preg = $this->rules[$k];
                    $data[$k] = Check::regularHandles($preg, $v);
                } else {
                    $data[$k] = $v;
                }
            }
        }
        if ($this->not_empty) {
            $this->notEmpty($data);
        }
        if ($table) {
            $data = $this->checkFields($data, $table, $pz);
        }
        return $data;
    }

    /**
     * 保存数据
     *
     * @param [type] $datas 数组
     * @param [type] $table 表名，用于验证数组中是否有和字段一样的键名
     * @param string $pz 数据库配置，用于连接不同的配置
     * @return array|int
     */
    final public function save($datas, $where = '', $pz = 'mysql')
    {
        $table = $this->table;
        $data = $this->checkValues($datas, $table, $pz);
        if (!empty($where)) {
            if (!$this->db::name($table)->where($where)->has()) {
                return 0;
            }
            $re = $this->db::name($table)->where($where)->upd($data);
            return $re;
        }
        $this->db::name($table)->add($data, 'replace');
        $id = $this->db::lastid();
        return $id;
    }

    /**
     * 验证过滤字段
     *
     * @param [type] $datas 数组
     * @param [type] $table 表名，用于验证数组中是否有和字段一样的键名
     * @param string $pz 数据库配置，用于连接不同的配置
     * @return array
     */
    final public function checkFields($datas, $table, $pz = 'mysql')
    {
        $re = $this->DB::switch($pz)::getAllField($table);
        // 获取主键
        $this->primary = $this->DB::switch($pz)::getPK($table);
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
}
