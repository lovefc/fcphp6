<?php

namespace FC\Db\Query;

/*
 * SqlJoin类
 * 
 * @Author: lovefc 
 * @Date: 2017/04/02 15:10
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-10-29 17:15:39
 */

trait SqlJoin
{

    //获取模式
    public $Mode;
    //数据库表名
    public $Table = '';
    //join
    public $Joinvar = '';
    //条件
    public $Where = '';
    //where条件的值
    public $Wdata = [];
    //排序
    public $Order = '';
    //limit
    public $Limit = '';
    //预处理的值
    public $Data = [];
    //记录一下每次执行的sql
    public $Sql = '';
    //记录一下每次执行的sql,用于本次操作
    public $Sqls = '';
    //要获取的字段
    public $Column = '*';
    // 返回值
    public $Return = false;
    // 主键名
    public $Primary = [];

    /**
     * 数据库表名
     *
     * @param [type] $table 表名
     * @return object
     */
    public function table($table)
    {
        if (!$table) {
            return $this;
        }
        $this->Table = $table;
        return $this;
    }

    /**
     * SQL语句
     *
     * @param [type] $sql
     * @return object
     */
    final public function sql($sql = null)
    {
        if (!is_null($sql)) {
            $this->Sql = $this->Sqls = $sql;
        }

        return $this;
    }

    /**
     * 传入预处理参数
     *
     * @param array $data
     * @return object
     */
    final public function data($data = array())
    {
        if (is_array($data)) {
            $this->Data = $data;
        }

        return $this;
    }

    /**
     * 获取字段名
     *
     * @param [type] $column
     * @return object
     */
    final public function getid($column = null)
    {
        $this->Column = ($column != null) ? $column : '*'; //默认为 *
        return $this;
    }

    /**
     * 排序
     * 
     * @param [type] $ziduan 字段名
     * @param string $sort desc 降序 asc 升序
     * @return object
     */
    final public function order($ziduan, $sort = 'desc')
    {
        $sort = strtoupper($sort) != 'DESC' ? 'ASC' : 'DESC';
        if (empty($this->Order)) {
            $this->Order = ' ORDER BY ' . $ziduan . ' ' . $sort;
        } else {
            $this->Order .= ',' . $ziduan . ' ' . $sort;
        }
        return $this;
    }

    /**
     * JOIN (要加表前缀)
     * user ON article.uid = user.uid
     * @param $table 表名称
     * @param $where 后面的条件
     * @param $type的类型如下：
     * null（内连接）：取得两个表中存在连接匹配关系的记录。
     * left（左连接）：取得左表（table1）完全记录，即是右表（table2）并无对应匹配记录。
     * right（右连接）：与 LEFT JOIN 相反，取得右表（table2）完全记录，即是左表（table1）并无匹配对应记录。
     * @return object
     */
    public function join($table, $where, $type = null)
    {
        if (!$table) {
            return $this;
        }
        switch ($type) {
            case 'left':
                $join = ' LEFT JOIN ';
                break;
            case 'right':
                $join = ' RIGHT JOIN ';
                break;
            default:
                $join = ' INNER JOIN ';
        }
        $this->Joinvar .= $join . $table . ' ON ' . $where;
        return $this;
    }

    /**
     * LIMIT 限定记录返回数量
     *
     * @param  $num  从那开始|记录行数
     * @param  $num2 从那开始|记录行数
     * @return object
     */
    final public function limit($start = null, $num = null)
    {
        if (!is_null($start)) {
            $start = (int) $start;
            $this->Limit = ' LIMIT ' . $start . ' ';
            if (!is_null($num)) {
                $num = (int) $num2;
                $this->Limit .= ',' . $num . ' ';
            }
        }
        return $this;
    }

    /**
     * 判断sql中的运算符号
     *
     * @param [type] $str
     * @return string
     */
    final public function is_operator($str)
    {
        if (!$str) {
            return $str;
        }
        $operator = array(
            '=',
            '<',
            '>',
            '!=',
            '!>',
            '!<',
            '<>',
            '>=',
            '<=',
        );
        if (in_array($str, $operator)) {
            return 'OPERATOR';
        } else {
            return $str;
        }
    }

    /**
     * 预处理解析where
     *
     * @param [type] $where
     * @return string
     */
    final public function pre_where($where)
    {
        $where2 = $where3 = $wdata = $wdata2 = array();
        if (is_array($where)) {
            array_filter($where);
            if (count($where) == 0) {
                return $this;
            }
            foreach ($where as $key => $value) {
                if (is_array($value)) {
                    $value[1] = strtoupper($value[1]);
                    $strs = $this->is_operator($value[1]);
                    switch ($strs) {
                        case 'IN':
                            if (is_array($value[2])) {
                                $str = rtrim(str_repeat('?,', count($value[2])), ',');
                                foreach ($value[2] as $values) {
                                    $wdata[] = $values;
                                }
                            } else {
                                $str = '?';
                                $wdata[] = $value[2];
                            }
                            $where3[] = ' ' . $value[0] . ' ' . $key . ' ' . $value[1] . '(' . $str . ')';
                            break;
                        case 'NOT IN':
                            if (is_array($value[2])) {
                                $str = rtrim(str_repeat('?,', count($value[2])), ',');
                                foreach ($value[2] as $values) {
                                    $wdata[] = $values;
                                }
                            } else {
                                $str = '?';
                                $wdata[] = $value[2];
                            }
                            $where3[] = ' ' . $value[0] . ' ' . $key . ' ' . $value[1] . '(' . $str . ')';
                            break;
                        case 'BETWEEN':
                            $where3[] = ' ' . $value[0] . ' ' . $key . ' ' . $value[1] . ' ? AND ? ';
                            $wdata[] = $value[2];
                            break;
                        case 'NOT BETWEEN':
                            $where3[] = ' ' . $value[0] . ' ' . $key . ' ' . $value[1] . ' ? AND ? ';
                            $wdata[] = $value[2];
                            break;
                        case 'LIKE':
                            $where3[] = ' ' . $value[0] . ' ' . $key . ' ' . $value[1] . ' ? ';
                            $wdata[] = $value[2];
                            break;
                        case 'NOT LIKE':
                            $where3[] = ' ' . $value[0] . ' ' . $key . ' ' . $value[1] . ' ? ';
                            $wdata[] = $value[2];
                            break;
                        case 'OPERATOR':
                            $where3[] = ' ' . $value[0] . ' ' . $key . ' ' . $value[1] . ' ? ';
                            $wdata[] = $value[2];
                            break;
                            // 新增，$where['a.title'] = array('AND','LOCATE',$s);
                        case 'LOCATE':
                            $where3[] = " " . $value[0] . " locate('" . $value[2] . "'," . $key . ")";
                            break;
                        default:
                            if (!isset($value[2])) {
                                if (!$this->Where) {
                                    $where2[] = ' ' . $key . ' ' . $value[0] . ' ?';
                                } else {
                                    $where2[] = ' && ' . $key . ' ' . $value[0] . ' ?';
                                }
                                $wdata2[] = $value[1];
                            } else {
                                $where3[] = ' ' . $value[0] . ' ' . $key . ' ' . $value[1] . ' ? ';
                                $wdata[] = $value[2];
                            }
                    }
                } else {
                    if (!$this->Where) {
                        $where2[] = $key . ' = ?';
                    } else {
                        $where2[] = ' && ' . $key . ' = ?';
                    }
                    $wdata2[] = $value;
                }
            }

            $where = implode(' && ', $where2) . implode('', $where3);
            $wdatas = array_merge($wdata2, $wdata);
            $this->Wdata = array_merge($this->Wdata, $wdatas);
        }
        return $where;
    }

    /**
     * 普通处理where
     *
     * @param [type] $where
     * @return string
     */
    public function pt_where($where)
    {
        $where2 = $where3 = $wdata = $wdata2 = array();
        if (is_array($where)) {
            array_filter($where);
            if (count($where) == 0) {
                return $this;
            }
            foreach ($where as $key => $value) {
                if (is_array($value)) {
                    $value[1] = strtoupper($value[1]);
                    $strs = $this->is_operator($value[1]);
                    switch ($strs) {
                        case 'IN':
                            if (is_array($value[2])) {
                                foreach ($value[2] as $values) {
                                    $wdata[] = $values;
                                }
                            } else {
                                $wdata[] = $value[2];
                            }
                            $where3[] = ' ' . $value[0] . ' ' . $key . ' ' . $value[1] . '(' . implode(',', $wdata) . ')';
                            break;
                        case 'NOT IN':
                            if (is_array($value[2])) {
                                foreach ($value[2] as $values) {
                                    $wdata[] = $values;
                                }
                            } else {
                                $wdata[] = $value[2];
                            }
                            $where3[] = ' ' . $value[0] . ' ' . $key . ' ' . $value[1] . '(' . implode(',', $wdata) . ')';
                            break;
                        case 'BETWEEN':
                            $where3[] = ' ' . $value[0] . ' ' . $key . ' ' . $value[1] . ' \'' . $value[2][0] . '\' AND \'' . $value[2][1] . '\' ';
                            break;
                        case 'NOT BETWEEN':
                            $where3[] = ' ' . $value[0] . ' ' . $key . ' ' . $value[1] . ' \'' . $value[2][0] . '\' AND \'' . $value[2][1] . '\' ';
                            break;
                        case 'LIKE':
                            $where3[] = ' ' . $value[0] . ' ' . $key . ' ' . $value[1] . ' \'' . $value[2] . '\'';
                            break;
                        case 'NOT LIKE':
                            $where3[] = ' ' . $value[0] . ' ' . $key . ' ' . $value[1] . ' \'' . $value[2] . '\'';
                            break;
                        case 'OPERATOR':
                            $where3[] = ' ' . $value[0] . ' ' . $key . ' ' . $value[1] . ' \'' . $value[2] . '\'';
                            break;
                            // 新增，$where['a.title'] = array('AND','LOCATE',$s);
                        case 'LOCATE':
                            $where3[] = " " . $value[0] . " locate('" . $value[2] . "'," . $key . ")";
                            break;
                        default:
                            if (!isset($value[2])) {
                                if (!$this->Where) {
                                    $where2[] = ' ' . $key . ' ' . $value[0] . ' \'' . $value[1] . '\'';
                                } else {
                                    $where2[] = ' && ' . $key . ' ' . $value[0] . ' \'' . $value[1] . '\'';
                                }
                            } else {
                                $where3[] = ' ' . $value[0] . ' ' . $key . ' ' . $value[1] . ' \'' . $value[2] . '\'';
                            }
                    }
                } else {
                    if (!$this->Where) {
                        $where2[] = $key . ' = \'' . $value . '\'';
                        $wdata2[] = $value;
                    } else {
                        $where2[] = ' && ' . $key . ' = \'' . $value . '\'';
                        $wdata2[] = $value;
                    }
                }
            }
            $where = implode(' && ', $where2) . implode('', $where3);
        }
        return $where;
    }

    /**
     * 解析where
     *
     * @param array $where 条件数组
     * @param bool $parsing 解析条件 false为普通解析，true为预处理解析
     * @return string
     */
    public function where_jx($where, $parsing)
    {
        if ($parsing == true) {
            $where = $this->pre_where($where);
        } else {
            $where = $this->pt_where($where);
        }
        return $where;
    }

    /**
     * 获取where
     * 
     * @param array $where 条件数组
     * @param bool $parsing 解析条件 false为普通解析，true为预处理解析
     * @return object
     */
    public function where($where = '', $parsing = true)
    {
        if (empty($where)) {
            return $this;
        }
        $where2 = $this->Where_jx($where, $parsing);
        if (!$this->Where) {
            $this->Where = 'WHERE ';
        }
        $this->Where .= ($where2 !== null) ? ' ' . $where2 : ' ';

        return $this;
    }

    /**
     * 生成select查询语句
     *
     * @param boolean $wy 查询前的值
     * @return object
     */
    public function select($wy = false)
    {
        $wy2 = $wy != false ? 'SELECT ' . $wy . ' ' : 'SELECT ';
        $this->Sql = $this->Sqls = empty($this->Sqls) ? $wy2 . $this->Column . ' FROM ' . $this->Table . ' ' . $this->Joinvar . ' ' . $this->Where . ' ' . $this->Order . ' ' . $this->Limit : $this->Sqls;
        return $this;
    }

    /**
     * 组合写入sql
     *
     * @param array $data
     * @param boolean $mode 插入方式
     * @param boolean $parsing 是否用预处理
     * @return object
     */
    public function insert($data, $mode = 'insert', $parsing = true)
    {
        if (!is_array($data)) {
            return false;
        }
        $sql1 = $sql2 = $value = null;
        foreach ($data as $k => $v) {
            if ($sql1) {
                $sql1 .= ',';
                $sql2 .= ',';
            }
            $sql1 .= "`{$k}`";
            if ($parsing == true) {
                $value[] = $v;
                $sql2 .= '?';
            } else {
                $sql2 .= "'" . $v . "'";
            }
        }
        switch ($mode) {
            case 'insert':
                $_sql = 'INSERT INTO';
                break;
            case 'ignore':
                $_sql = 'INSERT IGNORE INTO';
                break;
            case 'replace':
                $_sql = 'REPLACE INTO';
                break;
            default:
                $_sql = 'INSERT INTO';
        }
        $this->Sql = $this->Sqls = $_sql . " {$this->Table}({$sql1}) VALUES ({$sql2})";
        !is_null($value) && $this->Data = $value;
        return $this;
    }

    /**
     * 删除当前整表
     * 
     * @return object
     */
    public function drop()
    {
        $this->Sql = $this->Sqls = 'DROP TABLE IF EXISTS ' . $this->Table;
        return $this;
    }

    /**
     * 组合删除sql
     * 
     * @return object
     */
    public function delete()
    {
        if (!empty($this->Where)) {
            $this->Sql = $this->Sqls = "DELETE FROM " . $this->Table . " {$this->Where}";
        }
        return $this;
    }

    /**
     * 获取where
     * $where 条件数组
     * $jx 解析条件 false为普通解析，true为预处理解析
     */
    public function update($data = '', $parsing = true)
    {
        if (empty($data)) {
            return $this;
        }
        if ($parsing == true) {
            return $this->pre_update($data);
        } else {
            return $this->pt_update($data);
        }
    }

    /**
     * 组合更新sql(普通sql)
     *
     * @param array $data 数组键名代表字段名，键值表示要更新的值
     * @return object
     */
    public function pt_update($data)
    {
        $datas = array_values($this->Data);
        if (is_array($data)) {
            $str = '';
            foreach ($data as $key => $value) {
                //这一段检测更新字段加减的
                if (strpos($value, $key) === 0) {
                    $str .= '`' . $key . '`=' . $value . ',';
                    unset($data[$key]);
                } else {
                    $str .= '`' . $key . '`=\'' . $value . '\',';
                }
            }
            $strs = rtrim($str, ',');
            //$datas  = array_values(array_merge($datas, $data));
        } else {
            $strs = $data;
            $datas = $datas;
        }
        $this->Sql = $this->Sqls = "UPDATE " . $this->Table . " SET {$strs} {$this->Where}";
        $this->Data = $datas;
        return $this;
    }

    /**
     * 组合更新sql(预处理形式)
     *
     * @param array $data 数组键名代表字段名，键值表示要更新的值
     * @return object
     */
    public function pre_update($data)
    {
        $datas = array_values($this->Data);
        if (is_array($data)) {
            $str = '';
            foreach ($data as $key => $value) {
                if (strpos($value, $key) === 0) {
                    $str .= '`' . $key . '`=' . $value . ',';
                    unset($data[$key]);
                } else {
                    $str .= '`' . $key . '`=?,';
                }
            }
            $strs = rtrim($str, ',');
            $datas = array_values(array_merge($datas, $data));
        } else {
            $strs = $data;
            $datas = $datas;
        }
        $this->Sql = $this->Sqls = "UPDATE " . $this->Table . " SET {$strs} {$this->Where}";
        $this->Data = $datas;
        return $this;
    }

    /**
     * 初始化类变量
     *
     * @return void
     */
    public function uset()
    {
        $this->Joinvar = ''; //join
        $this->Sqls = '';
        $this->Data = array();
        $this->Wdata = array();
        $this->Column = '*';
        $this->Where = ''; //条件
        $this->Order = ''; //排序
        $this->Limit = ''; //limit
        $this->Mode = 2; //获取数据的模式
    }
}
