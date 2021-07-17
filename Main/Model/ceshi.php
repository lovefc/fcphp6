<?php

namespace Main\Model;

// 控制器基类
use FC\Model\BaseModel;

class ceshi extends BaseModel
{
	
    function __construct()
    {
		// 数据库配置类型,默认为Mysql
		//$this->db_type = 'Mysql';		
		// 数据库配置链接名称,默认为default
		//$this->db_config_name = 'default';
		//自定义数据库表名,默认是跟model文件名一样
		$this->table = 'ceshi';
		
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
		// 执行父类
        parent::__construct();		
    }
	
    // 这个是测试验证用户名的类方法
    public function name($a)
    {
        if ($a == 'fc') {
            return 'lovefc';
        }
    }
	
}