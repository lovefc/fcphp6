<?php

namespace Main\Controller;

use FC\Controller\BaseController;

/*
 * db 操作类
 * @Author: lovefc 
 * @Date: 2019-10-12 14:39:29
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-11-13 14:30:22
 */

class db extends BaseController
{
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

    // 获取表信息
    public function gettableinfo($table = 'ceshi')
    {
        $re = $this->SQLITE->table($table)->getTableInfo();
        \FC\pre($re);

        $re = $this->MYSQL->table($table)->getTableInfo();
        \FC\pre($re);
    }

    // where条件
    public function where($table = 'ceshi', $id = 'id', $value = 1)
    {
        $re = $this->SQLITE->table($table)->where([$id => $value])->fetch();
        \FC\pre($re);

        $re = $this->MYSQL->table($table)->where([$id => $value])->fetch();
        \FC\pre($re);
    }

     // 不使用where
     public function sql()
     {
         $sql = 'select * from ceshi';
         $re = $this->SQLITE->sql($sql)->fetchall();
         echo 'SQL：'.$this->SQLITE->lastsql();
         \FC\pre($re);
     }   

    // 创建数据库
    public function credb()
    {
        $newdb = 'db123';
        if ($this->MYSQL->newDB($newdb)) {
            echo '创建数据库成功';
        } else {
            echo '创建数据库失败';
        }
    }
	
	// 慢查询日志查询(需要在数据库配置中开启)
	public function getslowlog(){
		$re = $this->MYSQL->getSlowLog(20);
		\FC\pre($re);
	}
}
