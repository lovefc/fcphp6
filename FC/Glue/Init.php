<?php

namespace FC\Glue;

use FC\Route\Execs;

/*
 * 全局初始化类
 * @Author: lovefc 
 * @Date: 2019-09-20 14:04:27 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-09-20 15:52:27
 */

class Init extends Execs
{
    use \FC\Traits\Parents;

    // 运行
    public function run()
    {
        try {
            count($this->P_Config) > 1 ? ksort($this->P_Config) : '';
            $config = $this->P_Config;
            if (is_array($config) && count($config) >= 1) {
                foreach ($config as $value) {
                    if (is_array($value)) {
                        self::method($value);
                    } else {
                        self::func($value);
                    }
                }
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    // 错误消息
    public function error($msg)
    {
        \FC\Log::Show($msg);
    }
}
