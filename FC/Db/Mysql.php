<?php

namespace fcphp\extend\db;

/**
 * AUTHOR:lovefc
 * pdo - mysql
 */

class MySql extends PdoBase
{

    /**
     * 创建新用户,创建的用户拥有所有权限
     * @param $user 用户名
     * @param $pass 用户密码
     * @param $host 访问限制
     * @param $Host列指定了允许用户登录所使用的IP
     * Host=192.168.1.1。这里的意思就是说root用户只能通过192.168.1.1的客户端去访问。
     * 　　 * %是个通配符，如果Host=192.168.1.%，那么就表示只要是IP地址前缀为192.168.1.的客户端都可以连接。
     * 如果Host=%，表示所有IP都有连接权限。
     */
    public function newUser($user, $pass, $host = '127.0.0.1')
    {
        $re = $this->setTable('mysql.user')->where("User='" . $user . "'")->fetch();
        if ($re) {
            return false;
        }
        $sql = "insert into mysql.user values('{$host}','{$user}',password('{$pass}'),'Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','','','','','','','','','','')";
        $this->sql($sql)->query();
        if ($host != 'localhost') {
            $sql = "insert into mysql.user values('localhost','{$user}',password('{$pass}'),'Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','','','','','','','','','','')";
            $this->sql($sql)->query();
            $sql = "GRANT ALL PRIVILEGES ON *.* TO  '{$user}'@'localhost' WITH GRANT OPTION MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0";
            $this->sql($sql)->query();
        }
        $sql = "GRANT ALL PRIVILEGES ON *.* TO  '{$user}'@'{$host}' WITH GRANT OPTION MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0";
        if ($this->sql($sql)->query()) {
            $this->sql('flush privileges')->query();
            return true;
        } else {
            return false;
        }
    }

    //获取数据库表名
    public function getTable($table = null)
    {
        if (!$table) {
            return false;
        }
        return $this->Prefix . $table;
    }

    //缓慢查询日志
    public function slowlog($db)
    {
        //5.1.21版本以后才支持毫秒级的慢查询日志
        //if('5.1.21' > $this->version()){
        //$time = round($this->LongQueryTime);
        //}else{
        $time = $this->LongQueryTime;
        //}
        if ($time != 0) {
            $db->query("set global log_output='TABLE'");
            $db->query("set global log_slow_queries=ON");
            $db->query('set global long_query_time=' . $time);
        } else {
            if ($time === 0) {
                $db->query("set global log=OFF");
                $db->query("set global log_slow_queries=OFF");
            }
        }
    }


    //获取缓慢查询日志,$num 为日志数量
    public function getSlowLog($num = 1)
    {
        $num = (int)$num;
        if ($num > 1) {
            $sql = 'select * from mysql.slow_log order by 1 DESC LIMIT ' . $num;
            $re = $this->sql($sql)->fetchall();
        } else {
            $sql = 'select * from mysql.slow_log order by 1 DESC';
            $re = $this->sql($sql)->fetch();
        }
        if (is_array($re)) {
            return $re;
        }
        return false;
    }

    //复制一张表
    public function copyTable($newtable = null, $dbname = null)
    {
        $dbname = empty($dbname) ? $this->DbName : $dbname;
        if (empty($dbname) || empty($newtable) || empty($this->Table)) {
            return false;
        } else {
            $sql = 'create table ' . $dbname . '.' . $newtable . ' SELECT * FROM ' . $this->Table;
            if ($this->sql($sql)->query()) {
                return true;
            } else {
                return false;
            }
        }
    }

    //清空当前数据库表
    public function cleanTable($table = null)
    {
        $table = empty($table) ? $this->Table : $table;
        if (empty($table)) {
            return false;
        } else {
            if ($this->sql('truncate table ' . $table)->query()) {
                return true;
            } else {
                return false;
            }
        }
    }

    //修改数据库表名称
    public function newTableName($newtable = null, $table = null)
    {
        $table = empty($table) ? $this->Table : $table;
        if (empty($table) || empty($newtable)) {
            return false;
        } else {
            if ($this->sql('alter table ' . $table . ' rename to ' . $newtable)->query()) {
                return true;
            } else {
                return false;
            }
        }
    }

    //获取所有的数据库名
    public function getAllDBName()
    {
        $re = $this->sql("SELECT SCHEMA_NAME FROM information_schema.SCHEMATA")->fetchall();
        if (is_array($re)) {
            return array_column($re, 'SCHEMA_NAME');
        }
        return false;
    }

    //获取所有的字段名
    public function getAllField($table = null, $dbname = null)
    {
        $dbname = empty($dbname) ? $this->DbName : $dbname;
        $table = empty($table) ? $this->Table : $table;
        $re = $this->sql("select column_name from information_schema.columns where table_schema='" . $dbname . "' and table_name='" . $table . "'")->fetchall();
        if (is_array($re)) {
            return array_column($re, 'column_name');
        }
        return false;
    }

    //获取数据库表的大小,参数都为空，获取所有的表大小
    public function getTableSize($table = null, $dbname = null)
    {
        $dbname = empty($dbname) ? $this->DbName : $dbname;
        $table = empty($table) ? $this->Table : $table;
        if (empty($table)) {
            $sql = 'select sum(DATA_LENGTH)+sum(INDEX_LENGTH) as size from information_schema.tables';
        } else {
            $sql = "select sum(DATA_LENGTH)+sum(INDEX_LENGTH) as size from information_schema.tables where table_schema='" . $dbname . "' and table_name='" . $table . "'";
        }
        $re = $this->sql($sql)->fetch();
        if (is_array($re) && !empty($re['size'])) {
            return $this->getsize($re['size']);
        }
        return 0;
    }

    //获取数据库占用大小
    public function getDBSize($dbname = null)
    {
        $dbname = empty($dbname) ? $this->DbName : $dbname;
        $re = $this->sql("select sum(DATA_LENGTH)+sum(INDEX_LENGTH) as size from information_schema.tables where table_schema='" . $dbname . "'")->fetch();
        if (is_array($re) && !empty($re['size'])) {
            return $this->getsize($re['size']);
        }
        return 0;
    }

    //获取所有的表名
    public function getAllTable($dbname = null)
    {
        $dbname = empty($dbname) ? $this->DbName : $dbname;
        $re = $this->sql("select table_name from information_schema.tables where table_schema='" . $dbname . "' and table_type='base table'")->fetchall();
        if (is_array($re)) {
            return array_column($re, 'table_name');
        }
        return false;
    }

    //获取mysq版本号
    public function verSion()
    {
        $dbh = $this->link();
        $sth = $dbh->prepare('select version() as ver');
        $sth->execute();
        $re = $sth->fetch(\PDO::FETCH_ASSOC);
        return $re['ver'];
    }

    /**
     * 判断数据库表是否存在于数据库中
     */
    final public function hasTable($table = null, $dbname = null)
    {
        $dbname = empty($dbname) ? $this->DbName : $dbname;
        $table = empty($table) ? $this->Table : $table;
        if (empty($table) || empty($dbname)) {
            return false;
        }
        $sql = 'select table_name from information_schema.tables where table_schema=\'' . $dbname . '\' and table_name=\'' . $table . '\'';
        if ($this->sql($sql)->query()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 开始连接数据库
     */
    final public function link()
    {
        if (isset($this->DbObj[$this->ConfigName])) {
            return $this->DbObj[$this->ConfigName];
        }
        try {
            $dbh = 'mysql:host=' . $this->Host . ';port=' . $this->Port . ';dbname=' . $this->DbName;
            $db = new \PDO($dbh, $this->DbUser, $this->DbPwd, array(
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->Charset};",
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_PERSISTENT => $this->Attr,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ));
            //$db->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false); //关闭预处理
            $this->ConfigName = md5($dbh);
            $this->DbObj[$this->ConfigName] = $db;
            $this->slowlog($db); //缓慢日志查询
        } catch (\PDOException $e) {
            $error = array(
                'type' => $e->getcode(),
                'line' => $e->getline(),
                'message' => $e->getmessage(),
                'file' => $e->getfile()
            );
            WriteLog($error);
            $this->error($error['message']);
        }
        return $this->DbObj[$this->ConfigName];
    }

}
