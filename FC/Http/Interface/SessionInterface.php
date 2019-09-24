<?php
namespace FC\Http\Interface;

/*
 * @Author: lovefc 
 * @Date: 2019-09-09 09:59:49 
 * @Last Modified by: lovefc
 * @Last Modified time: 2019-09-24 16:16:38
 */

interface SessionInterface
{
    public function open();
    public function read($id);
	public function write($id, $data);
    public function close();
    public function destory($id);
    public function gc($maxlifetime)
}
