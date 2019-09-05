<?php

/**
 * session 操作类
 * 因为sowwle中没有session，需要自己写
 */ 
 
namespace swoole;

class Session
{
    private $sessionId;
    private $cookieKey;
    private $storeDir;
    private $file;
    private $isStart;
    
	//初始化
    public function __construct()
    {
        $this->cookieKey = 'PHPSESSID';
        $this->storeDir = 'tmp/';
        $this->isStart = false;
    }

	//启动写入
    public function start($request,$response)
    {
		if(empty($request) || empty($response)){
		    return false;
		}
        $this->isStart = true;
        $sessionId = $request->cookie[$this->cookieKey];
        if (empty($sessionId)){
            $sessionId = uniqid();
            $response->cookie($this->cookieKey, $sessionId);
        }
        $this->sessionId = $sessionId;
        $storeFile = $this->storeDir . $sessionId;
        if (!is_file($storeFile)) {
            touch($storeFile);
        }
        $session = $this->get($storeFile);
        $_SESSION = $session;
    }

	
    public function end()
    {
        $this->save();
    }

	//自动保存
    private function save()
    {
        if ($this->isStart) {
            $data = json_encode($_SESSION);
            ftruncate($this->file, 0);

            if ($data) {
                rewind($this->file);
                fwrite($this->file, $data);
            }
            flock($this->file, LOCK_UN);
            fclose($this->file);
        }
    }

	//获取解析文件内容
    private function get($fileName)
    {
        $this->file = fopen($fileName, 'c+b');
        if(flock($this->file, LOCK_EX | LOCK_NB)) {
            $data = [];
            clearstatcache();
            if (filesize($fileName) > 0) {
                $data = fread($this->file, filesize($fileName));
                $data = json_decode($data, true);
            }
            return $data;
        }
    }
}