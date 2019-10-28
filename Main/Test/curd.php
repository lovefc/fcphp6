<?php

namespace Main\Test;

// 控制器类
use FC\Controller\BaseController;

/*
 * curd 控制器操作
 * @Author: lovefc 
 * @Date: 2019-10-12 14:39:29
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-10-28 15:02:24
 */

class curd extends BaseController
{

    public function _init()
    {
        // 添加验证规则
        $rule = [
            // 常用的封装验证
            'age'    => '年龄',
            // 类方法验证
            'name'   => [$this, 'name'],
            // 正则验证
            'email'  => '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',
            // 匿名函数验证
            'mobile' => function ($mobile) {
                if (preg_match("/^((\(\d{3}\))|(\d{3}\-))?1[34578]\d{9}$/", $mobile)) {
                    return $mobile;
                }
            }
        ];
        // 添加规则
        $this->addRule($rule);
    }

    // 这个是测试验证用户名的类方法
    public function name($a)
    {
        if ($a == 'fc') {
            return 'my name is fc';
        }
    }

    // 全部验证
    public function check()
    {
        $datas = [
            'mobile'  => 15056003514,
            'email'   => 'fcphp@qq.com',
            'age'     => 20,
            'name'    => 'fc2'
        ];
        $re = $this->checkValues($datas);
        \FC\Pre($re);
    }    


    // 一个个的验证
    public function index($a = 25)
    {
        $re = $this->checkValue('mobile', $a);
        echo '手机：' . $re . FC_EOL;
        $re = $this->checkValue('email', $a);
        echo '邮箱：' . $re . FC_EOL;
        $re = $this->checkValue('age', $a);
        echo '年龄：' . $re . FC_EOL;
        $re = $this->checkValue('name', $a);
        echo '名称：' . $re . FC_EOL;
    }

    public function test()
    {
        echo 'mysql版本：' . $this->DB::verSion() . FC_EOL;
        echo 'sqlite版本：' . $this->DB::switch('sqlite')::verSion() . FC_EOL;
    }
}
