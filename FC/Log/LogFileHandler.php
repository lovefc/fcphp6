<?php
namespace FC\Log;
namespace FC\Log\Interface\LogHandler;

/*
 * @Author: lovefc 
 * @Date: 2019-09-09 10:25:16 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-09-09 10:26:37
 */

 
class LogFileHandler implements LogHandler
{
	private $handle = null;
	
	public function __construct($file = '')
	{
		$this->handle = fopen($file,'a');
	}
	
	public function write($msg)
	{
		fwrite($this->handle, $msg, 4096);
	}
	
	public function __destruct()
	{
		fclose($this->handle);
	}
}