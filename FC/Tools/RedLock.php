<?php

namespace FC\Tools;

/*
 * Redis 并发锁
 * @Author: lovefc
 * @Date: 2019-10-11 10:40:43 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-10-11 16:59:30
 */

class RedLock
{
    private $redis;
    private $lockKey;
    private $lockValue;

    /**
     * 构造
     *
     * @param array $config_name 配置名称
     */
    function __construct($config_name = 'redis')
    {
        $this->redis = \FC\obj('FC\Glue\Cache')->G($config_name);
    }

    /**
     * 加锁
     *
     * @param [type] $resource
     * @param [type] $ttl
     * @return void
     */
    public function lock($lockKey, $ttl)
    {
        $this->lockKey = $lockKey;
        $this->lockValue  = uniqid();
        return $this->redis->set($lockKey, $this->lockValue, ['NX', 'EX' => $ttl]);
    }

    /**
     * 解锁
     *
     * @param array $lock
     * 
     * @return void
     */
    public function unlock()
    {
        $script = "
            if redis.call('get',KEYS[1]) == ARGV[1] then
                return redis.call('del',KEYS[1])
            else
                return 0
            end
        ";
        $lockKey   = $this->lockKey;
        $lockValue = $this->lockValue;
        return $this->redis->eval($script, [$lockKey, $lockValue], 1);
    }
}
