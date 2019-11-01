<?php

namespace FC\Controller;

use FC\Json;

/**
 * 父类控制器，基础的控制器
 *
 * @Author: lovefc 
 * @Date: 2019-10-12 14:27:36 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-10-30 17:34:49
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
    public function checkClean()
    {
        if ($this->clean === true) {
            $table = $this->table;
            $this->db::cleanTable($table);
        }
    }

    // 删除操作
    public function checkDel($id, $field = '')
    {
        if (empty($id)) {
            return false;
        }
        $ids = (array) $id;
        $table = $this->table;
        // 获取主键
        if (!$this->primary) {
            $this->primary = $this->db::getPK($table);
        }
        $res = [];
        // 获取所有字段名
        $fields = $this->db::getAllField($table);
        foreach ($ids as $k => $kid) {
            if (in_array($kid, $this->keep)) {
                $res[$k] = 0;
                continue;
            }
            if (!$field) {
                $where[$this->primary] = $kid;
            } else {
                if (in_array($field, $fields)) {
                    $res[$k] = 0;
                    continue;
                }
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
     * 错误提示
     *
     * @param [type] $msg
     * @return void
     */
    public function error($code, $msg)
    {
        Json::error($code, $msg);
    }

     /**
     * 成功提示
     *
     * @param [type] $msg
     * @return void
     */
    public function success($data,$msg,$append=[])
    {
        Json::result($data,$msg,$append);
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
                $str = $v."值错误";
                $this->error(1,$str);
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
     * @return array
     */
    final public function checkValues($datas, $table = null)
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
            $data = $this->checkFields($data, $table);
        }
        return $data;
    }

    /**
     * 验证过滤输出
     *
     * @param [type] $datas 数组
     * @param [type] $table 表名，用于验证数组中是否有和字段一样的键名
     * @return array
     */
    final public function checkInputs($datas, $table = null)
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
        if ($table) {
            $data = $this->checkFields($data, $table);
        }
        return $data;
    }

    /**
     * 保存数据
     *
     * @param [type] $datas 数组
     * @param [type] $table 表名，用于验证数组中是否有和字段一样的键名
     * @return array|int
     */
    final public function checkSave($datas)
    {
        if (empty($datas)) {
            return 0;
        }
        $table = $this->table;
        $data = $this->checkValues($datas, $table);
        $this->db::name($table)->add($data, 'replace');
        $id = $this->db::lastid();
        return $id;
    }


    /**
     * 更新数据
     *
     * @param [type] $datas 数组
     * @param [type] $table 表名，用于验证数组中是否有和字段一样的键名
     * @return array|int
     */
    final public function checkUpdate($datas, $where = '')
    {
        if (empty($datas)) {
            return false;
        }
        $table = $this->table;
        $data = $this->checkValues($datas, $table);
        if (!empty($where)) {
            if (is_array($where)) {
                $where = $this->checkInputs($where, $table);
                if (empty($where)) {
                    return false;
                }
            }
            if (!$this->db::name($table)->where($where)->has()) {
                return false;
            }
            $re = $this->db::name($table)->where($where)->upd($data);
            return $re;
        }
        return false;
    }

    /**
     * 验证过滤字段
     *
     * @param [type] $datas 数组
     * @param [type] $table 表名，用于验证数组中是否有和字段一样的键名
     * @return array
     */
    final public function checkFields($datas, $table)
    {
        $re = $this->db::getAllField($table);
        // 获取主键
        $this->primary = $this->db::getPK($table);
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

    /**
     * rertful-get 获取数据
     *
     * @return void
     */
    public function get()
    {
        $datas  = \FC\input($_GET);
        $page   = (int)  isset($_GET['page']) ? $_GET['page'] : 1;
        $limit  = (int) isset($_GET['limit']) ? $_GET['limit'] : 0;
        $offset = (int) isset($_GET['offset']) ? $_GET['offset'] : 0;
        $skey   = isset($_GET['skey']) ? $_GET['skey'] : '';
        $sname  = isset($_GET['sname']) ? $_GET['sname'] : '';
        unset($datas['sname']); 
        unset($datas['skey']);
        unset($datas['page']);         
        // 表名
        $table = $this->table;
        // 检测变量值
        if (!empty($skey) && !empty($sname)) {
                // 搜索这个值
                $datas[$skey] = ['', 'LOCATE', $sname];
        }          
        $where  = $this->checkInputs($datas, $table);               
        // 排序方式
        $order  = (isset($_GET['order']) && $_GET['order'] === 'desc') ? 'desc'  : 'asc';
        // 排序字段 $this->primary这个值只有调用getAllField函数才会有值，所以放在后面检测
        $sortby = isset($_GET['sortby']) ? $_GET['sortby'] : $this->primary;
        // 获取数量
        $number   = $this->db::name($table)->where($where)->number();
        // 获取分页值
        list($total, $offset2) = $this->page($number, $page, $limit);
        // 看看从哪里开始
        if ($offset != 0) {
            $offset2 = $offset;
        }
        $limit = $offset2 . ',' . $limit;
        $res   = $this->db::name($table)->where($where)->order($sortby, $order)->limit($limit)->fetchall();
        //echo $this->db::lastsql();
        if ($res) {
            $this->success($res, '', ['page' => $page, 'number' => $number, 'total' => $total, 'offset' => $offset]);
        } else {
            $this->error(400, '没有数据');
        }
    }

    /**
     * 分页
     *
     * @param [type] $max
     * @param integer $page
     * @param integer $limit
     * @return string
     */
    public function page($max, $page = 1, $limit = 10)
    {
        if ($max == 0 || $limit == 0) {
            return [0, 0];
        }
        $total = ceil($max / $limit);
        if ($page > $total) {
            $page = 1;
        }
        $offset = $limit * ($page - 1);
        $offset = $offset < 0 ? 0 : $offset;
        return [$total, $offset];
    }

    /**
     * rertful-post 新增数据
     *
     * @return void
     */
    public function post()
    {
        $datas  = \FC\input($_POST);
        if ($id = $this->checkSave($datas)) {
            $this->success($res, '成功', ['id' => $id]);
        } else {
            $this->error(400, '失败');
        }
    }

    /**
     * rertful-put 更新数据,全部数据
     *
     * @return void
     */
    public function put()
    {
        $datas  = \FC\input($_POST);
        if ($id = $this->checkSave($datas)) {
            $this->success('', '成功', ['id' => $id]);
        } else {
            $this->error(400, '失败');
        }
    }


    /**
     * rertful-patch 根据条件更新数据
     *
     * @return void
     */
    public function patch()
    {
        $datas  = \FC\input($_POST);
        $data  = isset($datas['data']) ? $datas['data'] : '';
        $where = isset($datas['where']) ? $datas['where'] : '';
        if ($this->checkUpdate($datas, $where)) {
            $this->success('', '成功');
        } else {
            $this->error(400, '失败');
        }
    }

    /**
     * rertful-delete 删除数据
     *
     * @return void
     */
    public function delete()
    {
        $datas = \FC\input($_POST);
        $id = '';
        $field = isset($datas['field']) ? $datas['field'] : '';
        if (isset($datas['id'])) {
            if (is_array($datas['id'])) {
                $id = $datas['id'];
            } else {
                $id = explode(',', $datas['id']);
            }
        }
        if ($this->checkDel($id, $field)) {
            $this->success('', '删除成功');
        } else {
            $this->error(400, '删除失败');
        }
    }
}
