<?php

namespace FC\Tools;

use FC\Glue\Cache;

/*
 * Redis 分布式锁
 * @Author: linjiqin
 * @Date: 2019-10-11 10:40:43 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-10-11 11:08:15
 */

class RedLock
{
    private $retryDelay;

    private $retryCount;

    private $clockDriftFactor = 0.01;

    private $quorum;

    private $servers = array();

    private $instances = array();

    /**
     * 构造
     *
     * @param array $servers
     * @param integer $retryDelay
     * @param integer $retryCount
     */
    function __construct(array $servers, $retryDelay = 200, $retryCount = 3)
    {
        $this->servers = $servers;
        $this->retryDelay = $retryDelay;
        $this->retryCount = $retryCount;
        $this->quorum  = min(count($servers), (count($servers) / 2 + 1));
    }

    /**
     * 加锁
     *
     * @param [type] $resource
     * @param [type] $ttl
     * @return void
     */
    public function lock($resource, $ttl)
    {
        $this->initInstances();
        $token = uniqid();
        $retry = $this->retryCount;
        do {
            $n = 0;
            $startTime = microtime(true) * 1000;
            foreach ($this->instances as $instance) {
                if ($this->lockInstance($instance, $resource, $token, $ttl)) {
                    $n++;
                }
            }
            # Add 2 milliseconds to the drift to account for Redis expires
            # precision, which is 1 millisecond, plus 1 millisecond min drift
            # for small TTLs.
            $drift = ($ttl * $this->clockDriftFactor) + 2;
            $validityTime = $ttl - (microtime(true) * 1000 - $startTime) - $drift;
            if ($n >= $this->quorum && $validityTime > 0) {
                return [
                    'validity' => $validityTime,
                    'resource' => $resource,
                    'token'    => $token,
                ];
            } else {
                foreach ($this->instances as $instance) {
                    $this->unlockInstance($instance, $resource, $token);
                }
            }
            // Wait a random delay before to retry
            $delay = mt_rand(floor($this->retryDelay / 2), $this->retryDelay);
            usleep($delay * 1000);
            $retry--;
        } while ($retry > 0);
        return false;
    }

    /**
     * 解锁
     *
     * @param array $lock
     * @return void
     */
    public function unlock(array $lock)
    {
        $this->initInstances();
        $resource = $lock['resource'];
        $token    = $lock['token'];
        foreach ($this->instances as $instance) {
            $this->unlockInstance($instance, $resource, $token);
        }
    }

   /**
    * 实例化redis类
    *
    * @return void
    */
    private function initInstances()
    {
        if (empty($this->instances)) {
            foreach ($this->servers as $server) {
                list($host, $port, $timeout) = $server;
                /*
                $redis = new Redis();
                $redis->connect($host, $port, $timeout);
                */
                $this->instances[] = $redis;
            }
        }
    }

    /**
     * 锁定对象
     *
     * @param [type] $instance
     * @param [type] $resource
     * @param [type] $token
     * @param [type] $ttl
     * @return void
     */
    private function lockInstance($instance, $resource, $token, $ttl)
    {
        return $instance->set($resource, $token, ['NX', 'PX' => $ttl]);
    }

    /**
     * 解锁对象
     *
     * @param [type] $instance
     * @param [type] $resource
     * @param [type] $token
     * @return void
     */
    private function unlockInstance($instance, $resource, $token)
    {
        $script = '
            if redis.call("GET", KEYS[1]) == ARGV[1] then
                return redis.call("DEL", KEYS[1])
            else
                return 0
            end
        ';
        return $instance->eval($script, [$resource, $token], 1);
    }
}
