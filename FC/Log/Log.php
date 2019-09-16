<?php
namespace FC\Log;

/*
 * @Author: lovefc 
 * @Date: 2019-09-09 10:09:59 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-09-09 13:17:28
 */

 
class Log
{
    // 写入对象句柄
    private $handler = null;
    
    // 错误登记
	private $level = 15;
    
    // 保存单例
	private static $instance = null;
    
    // 防止实例化
    private function __construct(){}
        
    // 防止克隆
	private function __clone(){}
    
    /**
     * 初始函数，返回对象的单例
     *
     * @param [type] $handler
     * @param integer $level
     * @return objcet
     */
	public static function Init($handler = null,$level = 15)
	{
		if(!self::$instance instanceof self)
		{
			self::$instance = new self();
			self::$instance->__setHandle($handler);
			self::$instance->__setLevel($level);
		}
		return self::$instance;
	}
	
	/**
     * Undocumented function
     *
     * @param [type] $handler
     * @return void
     */
	private function __setHandle($handler){
		$this->handler = $handler;
	}
    
    // 设置错误登记
	private function __setLevel($level)
	{
		$this->level = $level;
	}
    
    // 记录debug级别错误
	public static function DEBUG($msg)
	{
		self::$instance->write(1, $msg);
	}
    
    // 记录warn级别错误
	public static function WARN($msg)
	{
		self::$instance->write(4, $msg);
	}
    
    // 错误追踪
	public static function ERROR($msg)
	{
		$debugInfo = debug_backtrace();
		$stack = "[";
		foreach($debugInfo as $key => $val){
			if(array_key_exists("file", $val)){
				$stack .= ",file:" . $val["file"];
			}
			if(array_key_exists("line", $val)){
				$stack .= ",line:" . $val["line"];
			}
			if(array_key_exists("function", $val)){
				$stack .= ",function:" . $val["function"];
			}
		}
		$stack .= "]";
		self::$instance->write(8, $stack . $msg);
	}
    
    // 记录info级别错误
	public static function INFO($msg)
	{
		self::$instance->write(2, $msg);
	}
    
    /**
     * 错误等级
     *
     * @param [type] $level
     * @return void
     */
	private function getLevelStr($level)
	{
		switch ($level)
		{
		case 1:
			return 'debug';
		break;
		case 2:
			return 'info';	
		break;
		case 4:
			return 'warn';
		break;
		case 8:
			return 'error';
		break;
		default:
				
		}
    }
    
    /**
     * 写入错误
     *
     * @param [type] $level
     * @param [type] $msg
     * @return void
     */
	protected function write($level,$msg)
	{
		if(($level & $this->level) == $level )
		{
			$msg = '['.date('Y-m-d H:i:s').']['.$this->getLevelStr($level).'] '.$msg."\n";
			$this->handler->write($msg);
		}
	}
}