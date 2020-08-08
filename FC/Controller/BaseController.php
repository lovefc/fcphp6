<?php

namespace FC\Controller;

use FC\Glue\Parts;

/**
 * 父类控制器，基础的控制器
 *
 * @Author: lovefc 
 * @Date: 2019-10-12 14:27:36 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-10-30 17:34:49
 */

abstract class BaseController
{
    // 跨域访问控制,默认是允许的
    public $cross = true;
	
	//当前的model类实例
	public $Model;

    // 初始化设置
    function __construct()
    {
		$cross = array_reduce(headers_list(),function($k,$v){
			if(strstr($v,'Access-Control-Allow')){
				return 0;
			}
			return 1;
		});		
        if ((boolval($this->cross) === true) && ($cross == 1)) {
            \FC\setOrigin(false, 'POST,GET,OPTIONS,PUT,DELETE', true);
        }
		$model_class_name = strtolower(basename(str_replace('\\', '/',get_class($this)))).'_model';
		$this->Model = $this->$model_class_name; 
    }
	
	// 获取model变量,并且自动实例化
    public function __get($name) 
    {
        $modelStr = substr($name, -5, 5);
		$this->$name = '';
		// 这里是约定model的变量形式 xxx_model 这种形式
	    if($modelStr==='model'){
			$classname = substr($name, 0, -6);
			$arr = explode('/',PATH['NOW']);
			$str = end($arr);
			$class = $str.'\\'.'Model'.'\\'.$classname;
			$this->$name = \FC\obj($class);
	    }
		// 这里是加载配置中定义的类库
		$glue = new Parts();
		if($obj = $glue->get($name)){
			$this->$name = $obj;
		}
		return $this->$name;
    }	
}
