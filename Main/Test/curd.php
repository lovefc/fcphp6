<?php

namespace Main\Test;

// 控制器类
use FC\Controller\BaseController;

/*
 * curd 控制器操作
 * @Author: lovefc 
 * @Date: 2019-10-12 14:39:29
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-10-24 16:27:52
 */

class curd extends BaseController
{
    use \FC\Traits\Parts;
    
    public function _init()
    {
        $this->rules  = [
            'age' => ['数字','','值必须是数字!']
        ];
    }

    public function index($a=25)
    {
        $re = $this->checkValue('age',$a);
        echo $re.FC_EOL;
        echo 'hello';
    }

    public function test(){
        $str = 'DESC  `ceshi`';
        $re = $this->MYSQL->sql($str)->fetchall();
        print_r($re);
    }
}
