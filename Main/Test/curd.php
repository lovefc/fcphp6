<?php

namespace Main\Test;

use FC\Json;

// 控制器基类
use FC\Controller\BaseController;

/*
 * curd 控制器操作
 * @Author: lovefc 
 * @Date: 2019-10-12 14:39:29
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-10-30 09:43:29
 */

class curd extends BaseController
{
    // 验证字段
    public function check()
    {
        $datas = [
            'sex'     => '',
            'mobile'  => 15056003514, // 假的号码
            'email'   => 'fcphp@qq.com',
            'age'     => '20',
            'name'    => 'fc'
        ];
        $re = $this->ceshi_model->checkInputs($datas);
        \FC\Pre($re);
    }
	
    // 过滤字段,匹配跟数据库中字段一样的键名名称
    public function filter()
    {
        $datas = [
            'sex'     => '',
            'mobile'  => 15056003514,
            'email'   => 'fcphp@qq.com',
            'age'     => '20',
            'name'    => 'fc',
			'xxxx'    => '123',
			'type'    => '456',
			'url'     => 'https://lovefc.cn'
        ];
        $re = $this->ceshi_model->filterValue($datas);
        \FC\Pre($re);
    }
	
	
    // 保存数据
    public function add($age = 20, $name = 'fc')
    {
        $datas = [
            'age'     => $age,
            'name'    => $name
        ];
        if ($this->ceshi_model->checkSave($datas)) {
            echo '插入成功';
        } else {
            echo '插入失败';
        }
    }

    // 保存并更新数据，要带主键
    public function upd()
    {
        $datas = [
            'id'      => 1,
            'age'     => '33',
            'name'    => 'fc'
        ];
        if ($this->ceshi_model->checkSave($datas)) {
            echo '更新成功';
        } else {
            echo '更新失败';
        }
    }

    // 更新数据,带where
    public function upd2($age = '20')
    {
        $datas = [
            'age'     => '10',
            'name'    => 'fc'
        ];
        $where['age'] = $age; // 也可以这样写 $where = 'age=20';
        // 如果数据不存在，会返回空值，否则会返回被影响的行数。
        if ($id = $this->ceshi_model->checkUpdate($datas, $where)) {
            Json::result('', '更新成功');
        } else {
            Json::error(400, '更新失败');
        }
    }

    // 一个个的验证
    public function index($a = 25)
    {
        $re = $this->ceshi_model->checkInput('mobile', $a);
        echo '手机：' . $re . FC_EOL;
        $re = $this->ceshi_model->checkInput('email', $a);
        echo '邮箱：' . $re . FC_EOL;
        $re = $this->ceshi_model->checkInput('age', $a);
        echo '年龄：' . $re . FC_EOL;
        $re = $this->ceshi_model->checkInput('name', $a);
        echo '名称：' . $re . FC_EOL;
    }

    // 清空数据库
    public function clean()
    {
        $this->ceshi_model->checkClean();
    }
}
