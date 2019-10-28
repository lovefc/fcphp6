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
        $size = $this->DB::switch('sqlite')::getDBSize();
        echo 'Sqlite版本：' . $this->DB::switch('sqlite')::verSion() . ' 大小：' . $size[0] . $size[1] . FC_EOL;
        
        $size = $this->DB::getDBSize();
        echo 'Mysql版本：' . $this->DB::verSion() . ' 大小：' . $size[0] . $size[1] . FC_EOL;
    }

    // 获取表的一个值
    public function get($table = 'ceshi', $limit = 1)
    {
        $re = $this->DB::switch('sqlite')::table($table)->limit(1)->fetch();
        \FC\pre($re);
        
        $re = $this->DB::table($table)->limit(1)->fetch();
        \FC\pre($re);        
    }

    // 获取数据库中的所有表
    public function gettable()
    {
        $re = $this->DB::switch('sqlite')::getAllTable();
        \FC\pre($re);

        $re = $this->DB::getAllTable();
        \FC\pre($re);
    }

    // 获取表中的所有字段
    public function getfield($table = 'ceshi')
    {
        $re = $this->DB::switch('sqlite')::table($table)->getAllField();
        \FC\pre($re);
        
        $re = $this->DB::table($table)->getAllField();
        \FC\pre($re);
    }
    
    // where条件
    public function where($table = 'ceshi', $id = 'id', $value = 1)
    {
        $re = $this->DB::switch('sqlite')::table($table)->where([$id=>$value])->fetch();
        \FC\pre($re);
        
        $re = $this->DB::table($table)->where([$id=>$value])->fetch();
        \FC\pre($re);
    }    
}
