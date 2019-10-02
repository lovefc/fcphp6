<?php

namespace FC\Cache;

/**
 * redis协议类
 * author:lovefc
 * time:2017/07/26
 * 只是封装了基本的操作，更多的操作可以去扩展
 * 更多命令可参考 http://www.redis.net.cn/order/
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-10-03 00:26:43
 */

class Redis
{

    private $connection;

    /**
     * 链接redis
     * @param $host 主机
     * @param $port 端口
     * @param $time 时间(单位为s)
     * @return bool
     */
    public function connect($host, $port = 6379, $time = 1)
    {
        try {
            $connection = fsockopen($host, $port, $errorN, $errorStr, $time);
            $this->connection = $connection;
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        return true;
    }

    /*
     * 执行redis命令
     */

    public function command($command, $ar = array())
    {
        return $this->runCommand($this->mkCommand($command, $ar));
    }

    /**
     * 拼接处理命令
     * @param $command
     * @param $ar
     * @return string
     */
    private function mkCommand($command, $ar = array())
    {
        $count = count($ar);
        for ($i = 0; $i < $count; $i++) {
            $command .= ' "' . str_replace(array("\n", "\r"), array('\n', '\r'), $ar[$i]) . '"';
        }
        return $command;
    }

    /**
     * 执行命令
     * @param $command
     * @return array|bool|int|string
     */
    private function runCommand($command)
    {
        $handle = $this->connection;
        fwrite($handle, $command . "\r\n");
        $fl = fgets($handle); //fl:First Line
        $re = false;     
        switch ($fl[0]) {
            case '+':
                $re = true;
                break;
            case '-':
                throw new \Exception($fl);
                break;
            case ':':
                $re = (int)(substr($fl, 1, -2));
                break;
            case '$':
                $len = (int)(substr($fl, 1, -2)) + 2;
                $size = 0;
                while ($size < $len) {
                    $re .= fgets($handle);
                    $size = strlen($re);
                }
                $re = substr($re, 0, $len - 2);
                break;
            case '*':
                $re = array();
                $count = (int)(substr($fl, 1, -2));
                for ($i = 0; $i < $count; $i++) {
                    $l = fgets($handle);
                    $len = (int)(substr($l, 1, -2));
                    $size = 0;
                    $str = '';
                    while ($size < $len) {
                        $size = strlen($str .= fgets($handle));
                    }
                    $str = substr($str, 0, $len);
                    $re[] = $str;
                }
                break;
        }
        return $re;
    }

    /**
     * 设置一个key
     * @param $key    键名
     * @param $value  键值
     * @param $expire 过期时间
     * @return array|bool|int|string
     */
    public function set($key, $value, $expire = null)
    {   
        $re = $this->command('SET', array(
            $key,
            $value
        ));
        if (!empty($expire)) {
            $this->command('EXPIRE', array(
                $key,
                (int)$expire
            ));
        }
        return $re;
    }
    
    /**
     * 获取一个key
     * @param $key
     * @return array|bool|int|string
     */
    public function get($key)
    {    
        if($this->has($key) == false){
            return false;
        }
        return $this->command('GET', array(
            $key
        ));
    }

    /**
     * 判断一个key是否存在或者过期(如果有第二个参数就是判断哈希表中的字段了)
     * @param $key
     * @return true|false
     */
    public function has($key, $field = null)
    {
        if (empty($field)) {
            return $this->command('EXISTS', array(
                $key
            ));
        } else {
            return $this->command('HEXISTS', array(
                $key,
                $field
            ));
        }
    }

    /**
     * 删除一个key(如果有第二个参数就是删除哈希表中的字段了)
     * @param $key
     * @return int 返回被删除的数量
     */
    public function del($key, $field)
    {
        if (empty($field)) {
            return $this->command('DEL', array(
                $key
            ));
        } else {
            return $this->command('HDEL', array(
                $key,
                $field
            ));
        }
    }

    /**
     * 删除所有的key
     * @return array|bool|int|string
     */
    public function flushall()
    {
        return $this->runCommand('FLUSHALL');
    }

    /**
     * 将哈希表 key 中的字段 field 的值设为 value
     * @param $key
     * @param $field
     * @param $value
     * @return array|bool|int|string
     */
    public function hset($key, $field, $value)
    {
        return $this->command(
            'HSET', array(
                $key,
                $field,
                $value
            )
        );
    }

    /**
     * 获取在哈希表中指定 key 的所有字段和值
     * @param $key
     * @return array
     */
    public function hgetall($key)
    {
        $re = $this->command(
            'HGETALL', array(
                $key
            )
        );
        $return = array();
        $count = count($re) / 2;
        for ($i = 0; $i < $count; $i++) {
            $return[$re[$i * 2]] = $re[$i * 2 + 1];
        }
        return $return;
    }

    /**
     * 获取存储在哈希表中指定字段的值
     * @param $key
     * @param $field
     * @return array|bool|int|string
     */
    public function hget($key, $field)
    {
        return $this->command(
            'HGET', array(
                $key,
                $field
            )
        );
    }

    /**
     * 获取哈希表中所有值
     * @param $key
     * @return array|bool|int|string
     */
    public function hvals($key)
    {
        return $this->command(
            'HVALS', array(
                $key
            )
        );
    }

    /**
     * redis信息
     * @return string
     */
    public function info()
    {
        return $this->runCommand('INFO');
    }

    /**
     * 获取版本
     * @return mixed
     */
    public function ver()
    {
        $info = $this->info();
        preg_match_all('/redis_version:([0-9\.]+)/', $info, $matches);
        return $matches[1][0];
    }

    /**
     * 获取存储在哈希表中指定字段的值的长度
     * @param $key
     * @param $field
     * @return int
     */
    public function hstrlen($key, $field)
    {
        return $this->command(
            'HSTRLEN', array(
                $key,
                $field
            )
        );
    }

    /**
     * 将 key 中储存的数字值增一
     * @param $key
     * @return int
     */
    public function incr($key)
    {
        return $this->command(
            'INCR', array(
                $key
            )
        );
    }

    /**
     * 将 key 中储存的数字值减一
     * @param $key
     * @return int
     */
    public function decr($key)
    {
        return $this->command(
            'DECR', array(
                $key
            )
        );
    }

    /**
     * 返回key存储的value的长度
     * @param $key
     * @return int
     */
    public function strlen($key)
    {
        return $this->command(
            'STRLEN', array(
                $key
            )
        );
    }
   
	/**
	 * 指定的 key 不存在时，为 key 设置指定的值
	 * @param $key
	 * @param $value
	 * @return 0|1
	 */
	public function setnx($key,$value){
        return $this->command('EXPIRE', array(
            $key,
            $value
        ));	
	}
	
    /**
     * 给key增加相应的数量
     * @param $key
     * @param $value
     * @return int
     */
    public function incrby($key, $value)
    {
        return $this->command(
            'INCRBY', array(
                $key,
                $value
            )
        );
    }
	
    /**
     * 给key减去相应的数量
     * @param $key
     * @param $value
     * @return int
     */
    public function decrby($key, $value)
    {
        return $this->command(
            'DECRBY', array(
                $key,
                $value
            )
        );
    }
	
    /*
     * 打印错误
     */

    public function error($msg)
    {
        die($msg);
    }

}
