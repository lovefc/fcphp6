<?php

namespace Main\Test;

/*
 * db 操作类
 * @Author: lovefc 
 * @Date: 2019-10-12 14:39:29
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-10-16 16:12:01
 */

class db
{
    use \FC\Traits\Parts;

    // 打印信息
    public function index()
    {
        $size = $this->SQLITE->getDBSize();
        echo 'Sqlite版本：' . $this->SQLITE->verSion() . ' 大小：' . $size[0] . $size[1] . FC_EOL;
        
        $size = $this->MYSQL->getDBSize();
        echo 'Mysql版本：' . $this->MYSQL->verSion() . ' 大小：' . $size[0] . $size[1] . FC_EOL;
    }

    // 获取表的一个值
    public function get($table = 'ceshi', $limit = 1)
    {
        $re = $this->SQLITE->table($table)->limit(1)->fetch();
        \FC\pre($re);
        
        $re = $this->MYSQL->table($table)->limit(1)->fetch();
        \FC\pre($re);        
    }

    // 获取数据库中的所有表
    public function gettable()
    {
        $re = $this->SQLITE->getAllTable();
        \FC\pre($re);

        $re = $this->MYSQL->getAllTable();
        \FC\pre($re);
    }

    // 获取表中的所有字段
    public function getfield($table = 'ceshi')
    {
        $re = $this->SQLITE->table($table)->getAllField();
        \FC\pre($re);
        
        $re = $this->MYSQL->table($table)->getAllField();
        \FC\pre($re);
    }
    
    // where条件
    public function where($table = 'ceshi', $id = 'id', $value = 1)
    {
        $re = $this->SQLITE->table($table)->where([$id=>$value])->fetch();
        \FC\pre($re);
        
        $re = $this->MYSQL->table($table)->where([$id=>$value])->fetch();
        \FC\pre($re);
    }    
}
