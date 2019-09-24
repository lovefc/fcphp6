<?php

namespace FC\Http;

use FC\Http\Interface\SessionInterface;

/*

 * @Author: lovefc 
 * @Date: 2019-09-09 10:25:16 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-09-24 16:31:06
 */

 
class FilesHandler implements SessionInterface
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