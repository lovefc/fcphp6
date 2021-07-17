<?php

namespace Main\Model;

// 控制器基类
use FC\Model\BaseModel;

class goods extends BaseModel
{
	
    function __construct()
    {
		// 数据库配置类型,默认为Mysql
		//$this->db_type = 'Mysql';		
		// 数据库配置链接名称,默认为default
		//$this->db_config_name = 'default';
		//自定义数据库表名,默认是跟model文件名一样
		//$this->table = 'goods';		
		// 执行父类
        parent::__construct();		
    }
	
}