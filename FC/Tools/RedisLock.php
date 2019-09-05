<?php

namespace fcphp\extend;

class RedisLock
{
    public $objRedis = null;
    
    public $timeout = 3;
    /**
     * @desc 设置redis实例
     *
     * @param obj object | redis实例
     */
    public function __construct($obj)
    {
        $this->objRedis = $obj;
    }

    /**
     * @desc 获取锁键名
     */
    public function getLockCacheKey($key)
    {
        return "lock_{$key}";
    }

    /**
     * @desc 获取锁
     *
     * @param key string | 要上锁的键名
     * @param timeout int | 上锁时间
     */
    public function getLock($key, $timeout = NULL)
    {
        $timeout = $timeout ? $timeout : $this->timeout;
        $lockCacheKey = $this->getLockCacheKey($key);
        $expireAt = time() + $timeout;
        $isGet = (bool)$this->objRedis->setnx($lockCacheKey, $expireAt);
        if ($isGet) {
            return $expireAt;
        }

        while (1) {
            usleep(10);
            $time = time();
            $oldExpire = $this->objRedis->get($lockCacheKey);
            if ($oldExpire >= $time) {
                continue;
            }
            $newExpire = $time + $timeout;
            $expireAt = $this->objRedis->($lockCacheKey, $newExpire);
            if ($oldExpire != $expireAt) {
                continue;
            }
            $isGet = $newExpire;
            break;
        }
        return $isGet;
    }

    /**
     * @desc 释放锁
     *
     * @param key string | 加锁的字段
     * @param newExpire int | 加锁的截止时间
     *
     * @return bool | 是否释放成功
     */
    public function releaseLock($key, $newExpire)
    {
        $lockCacheKey = $this->getLockCacheKey($key);
        if ($newExpire >= time()) {
            return $this->objRedis->del($lockCacheKey);
        }
        return true;
    }
}