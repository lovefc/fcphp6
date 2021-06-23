<?php

namespace Main\Controller;

use FC\Controller\BaseController;

use \FC\Tools\RedLock;

/*
 * redis demo类
 * @Author: lovefc 
 * @Date: 2019-10-12 08:55:00 
 * @Last Modified by: lovefc
 * @Last Modified time: 2021-06-23 11:08:37
 */

class redis extends BaseController
{
    // 设置redis的值
    public function set($k = 'a', $v = 'hello')
    {
        if ($this->REDIS->set($k, $v)) {
            echo 'redis设置成功';
        } else {
            echo 'redis设置失败';
        }
        // 设置一个过期时间
        $this->REDIS->expire($k, 10);
    }

    // 读取redis的值
    public function get($k = 'a')
    {
        echo $this->REDIS->get($k);
    }

    // redis工具
    public function access()
    {
        // 实例化工具类，可以传入在cache.php中的配置键名，注意要是redis链接
        $redLock = new RedLock('redis');
        // 在10秒，如果访问10次
        $r = $redLock->access('sss', 10, 10);
        if ($r == false) {
            echo '限制访问';
        } else {
            echo $r;
        }
    }

    // 并发锁实例
    // 可用ab测试 ab -c 1000 -n 1000 http://xxx/redis/lock
    public function lock()
    {
        $redLock = new RedLock('redis');
        // 设置一个锁和它的时长（秒）
        $lock = $redLock->lock('lock', 2);
        if ($lock) {
            /** 若干操作 **/
            file_put_contents('lock.log', 'true' . PHP_EOL, FILE_APPEND);

            // 最后一定要解锁
            $redLock->unlock();
        } else {
            file_put_contents('lock.log', 'false' . PHP_EOL, FILE_APPEND);
        }
    }
}
