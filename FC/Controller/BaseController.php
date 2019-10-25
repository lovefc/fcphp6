<?php

namespace FC\Controller;

/**
 * 增删改查
 *
 * @Author: lovefc 
 * @Date: 2019-10-12 14:27:36 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-10-24 17:20:15
 */

abstract class BaseController
{
    use \FC\Traits\Parts;

    // 规则
    public $rules = [];

    // 增加规则
    final public function addRule($name, $array='')
    {
        if(is_array($name)){
            foreach($name as $k=>$v){
                $this->rules[$k] = $v;
            }                
        }
        if ($name && $array) {
            $this->rules[$name] = $array;
        }
    }
    // 检测值是否存在
    final public function checkValue($key, $value)
    {
        $value = $value;
        $kes = $this->rules[$key];
        try {
            $status = Check::regularHandles($kes, $value);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        return $status;
    }


    public function error($msg, $e = '')
    {
        die($msg);
    }
}
