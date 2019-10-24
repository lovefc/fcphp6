<?php

namespace Main\Test;

// 控制器类
use FC\Controller\BaseController;

/*
 * curd 控制器操作
 * @Author: lovefc 
 * @Date: 2019-10-12 14:39:29
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-10-24 17:19:48
 */

class curd extends BaseController
{
    
    public function _init()
    {
        // 添加验证规则
        $this->addRule('age','数字');
    }


    public function index($a=25)
    {
        $re = $this->checkValue('age',$a);
        echo $re.FC_EOL;
        echo 'hello';
    }

    public function test(){
        $re = $this->MYSQL->getAllField('ceshi');
        print_r($re);

    }
}
