<?php

namespace FC\Db;

use FC\Db\Base\PdoBase;

/*
 * SQLITE类
 * 
 * @Author: lovefc 
 * @Date: This was written in 2017
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-10-12 15:28:54
 */

class Sqlite extends PdoBase {

    /**
     * 获取数据库表名
     *
     * @param [type] $table 表名
     * @return 
     */
    public function getTable($table = null) {
        if (!$table) {
            return false;
        }
        return $this->Prefix . $table;
    }

    /**
     * 获取sqlite数据库大小
     *
     * @return array
     */
    public function getDBSize() {
        $file = $this->DbName;
        if (is_file($file)) {
            return $this->getsize(filesize($file));
        }
        return false;
    }

    /**
     * 获取sqlite版本号
     *
     * @return string
     */
    public function verSion() {
        $dbh = $this->link();
        $sth = $dbh->prepare('select sqlite_version(*) as ver');
        $sth->execute();
        $re = $sth->fetch(\PDO::FETCH_ASSOC);
        return $re['ver'];
    }

    /**
     * 获取所有的字段名
     *
     * @param [type] $table
     * @return array
     */
    public function getAllField($table = null)
    {
        $table = empty($table) ? $this->Table : $table;
        $re = $this->sql("PRAGMA table_info([{$table}])")->fetchall();
        if (is_array($re)) {
            $arr = [];
            $i = 0;
            foreach($re as $v){
                $arr[$i] = $v['name'];
                $i++;
            }
            return $arr;
        }
        return false;
    }   

     /**
     * 获取所有的数据库名
     *
     * @return array
     */
    public function getAllTable()
    {
        $re = $this->sql("select * from sqlite_master WHERE type = 'table'")->fetchall();
        if (is_array($re)) {
            $arr = [];
            $i = 0;
            foreach($re as $v){
                $arr[$i] = $v['tbl_name'];
                $i++;
            }
            return $arr;
        }
        return false;
    }

    /**
     * 开始连接数据库
     *
     * @return object
     */
    final public function link() {
        if (isset($this->DbObj[$this->ConfigName])) {
            return $this->DbObj[$this->ConfigName];
        }
        try {
            $dbh = 'sqlite:' . $this->DbName;
            $db = new \PDO($dbh, $this->DbUser, $this->DbPwd, array(
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->Charset};",
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_PERSISTENT => $this->Attr,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ));
            // 设置禁止本地模拟prepare
            $db->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
            $this->ConfigName = md5($dbh);
            $this->DbObj[$this->ConfigName] = $db;
        } catch (\PDOException $e) {
            $error = array(
                'type' => $e->getcode(),
                'line' => $e->getline(),
                'message' => $e->getmessage(),
                'file' => $e->getfile()
            );
            \FC\Log::WriteLog($error);
            $this->error($error['message']);
        }
        return $this->DbObj[$this->ConfigName];
    }

}
