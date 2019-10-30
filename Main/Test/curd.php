<?php

namespace Main\Test;

/*
 * 继承框架提供的控制器
 * @Author: lovefc 
 * @Date: 2019-10-30 15:42:03 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-10-30 15:42:36
 */

// 控制器类
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
    use \FC\Traits\Parts;

    public function _init()
    {
        // 数据库句柄
        $this->db = $this->DB;

        // 表名
        $this->table = 'ceshi';

        // 是否允许数据表被清空
        $this->clean = true;

        // 保留的数据
        $this->keep = [1];

        // 是否可以跨域
        $this->cross = true;

        // 添加验证规则
        $this->rules = [
            // 验证非空
            'sex'    => 'empty',
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

        // 验证非空，并返回错误
        $this->not_empty = ['age', 'name'];
    }

    // 这个是测试验证用户名的类方法
    public function name($a)
    {
        if ($a == 'fc') {
            return 'lovefc';
        }
    }

    // 验证字段
    public function check()
    {
        $datas = [
            'sex'     => '',
            'mobile'  => 15056003514,
            'email'   => 'fcphp@qq.com',
            'age'     => '20',
            'name'    => 'fc'
        ];
        $re = $this->checkValues($datas, $this->table);
        \FC\Pre($re);
    }

    // 保存数据
    public function add($age = 20, $name = 'fc')
    {
        $datas = [
            'age'     => $age,
            'name'    => $name
        ];
        if ($this->save($datas)) {
            echo '插入成功';
        } else {
            echo '插入失败';
        }
    }

    // 自动更新数据，要带主键
    public function upd()
    {
        $datas = [
            'id'      => 1,
            'age'     => '33',
            'name'    => 'fc'
        ];
        if ($this->save($datas)) {
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
        if ($this->save($datas, $where)) {
            echo '更新成功';
        } else {
            echo '更新失败';
        }
    }

    // 删除数据
    public function del()
    {
        $id = [
            1, 6, 7
        ];
        //$field = '字段名'; $this->delete($id,$field);
        if ($this->delete($id)) {
            echo '删除成功';
        } else {
            echo '删除失败';
        }
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

    public function clean()
    {
        parent::clean();
    }
}
