<?php

namespace Main\Test;

// 控制器类
use FC\Controller\BaseController;

/*
 * curd 控制器操作
 * @Author: lovefc 
 * @Date: 2019-10-12 14:39:29
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-10-25 14:48:24
 */

class curd extends BaseController
{

    public function _init()
    {
        // 添加验证规则
        $rule = [
            'age' => [$this, 'a'],
            'name' => [$this, 'a'],
            'email' => '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',
            'url'   => '/^(http|ftp|https|ftps):\/\/([a-z0-9\-_]+\.)/i'
        ];
        $this->addRule($rule);
    }

    public function a($a)
    {
        return 6666;
    }

    public function index($a = 25)
    {
        $re = $this->checkValue('url', $a);
        echo $re . FC_EOL;
        $re = $this->checkValue('email', $a);
        echo $re . FC_EOL;       
        echo 'hello';
    }

    public function test()
    {
        //$re = $this->DB::getAllField('ceshi');
        //print_r($re);
        
        $table = 'ceshi';
        
        echo $this->DB::verSion().PHP_EOL;
        
        //echo $this->DB::verSion();      
    }
}
