<?php

namespace FC\Controller;

use FC\Route\Execs;

/**
 * 增删改查
 *
 * @Author: lovefc 
 * @Date: 2019-10-12 14:27:36 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-10-12 14:34:19
 */

abstract class BaseController
{
    // 规则
    public $rules = array(); 
    
    // 检测值是否存在
    final public function checkValue($key, $value)
    {
        $value = $value;
        $kes = $this->rules[$key];
        try {
            $status = Execs::regularHandle($kes, $value);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        return $status;
    }
    
    public function error($msg,$e=''){
        die($msg);   
    }
}
