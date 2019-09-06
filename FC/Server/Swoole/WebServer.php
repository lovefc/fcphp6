<?php

/**
 * 服务器类
 * author:lovefc
 */ 
 
namespace swoole;

class WebServer{
	
	private $request = [];
	
	private $response = [];
	
	private $session  = '';
	
	private $sessionStatus = 0;//打开关闭
	
	public $http;
	
	private static $instance;//单例对象
	
	//单例对象
    public static function getInstance()
    {
        return self::$instance;
    }
	
	//主机和端口
	public function getHttpHost(){
		return $this->request->header['host'];
	}
	
	//connection
	public function getHttpConnection(){
		return $this->request->header['connection'];
	}
	
	//accept
	public function getHttpAccept(){
		return $this->request->header['accept'];
	}	
	
	//accept-language
	public function getHttpAcceptEncoding(){
		return $this->request->header['accept-encoding'];
	}		
	
	//accept-language
	public function getHttpAcceptLanguage(){
		return $this->request->header['accept-language'];
	}		
	
	//获取用户浏览器信息
	public function getHttpUserAgent(){
		return $this->request->header['user-agent'];
	}	
	
    //获取请求方式
    public function getRequestMethod(){
        return $this->request->server['request_method'];
    }
	
    //获取时间戳
    public function getRequestTime(){
        return $this->request->server['request_time'];
    }
	

    //获取时间戳浮点数
    public function getRequestTimeFloat(){
        return $this->request->server['request_time_float'];
    }
	
	//获取protocol
	public function getServerProtocol(){
		return $this->request->server['server_protocol'];
	}
	
	//获取swoole标识
	public function getServerSoftware(){
		return 'swoole-http-server';
	}
		
	//获取服务器地址
	public function getServerAddr(){
		$host = explode(":",$this->getHttpHost());
		return $host[0];
	}	
	
	
	//获取服务器端口
	public function getServerPort(){
		return $this->request->server['server_port'];
	}


	//获取request_uri
	public function getRequestUri(){
		return $this->request->server['request_uri'];
	}
	
	//获取path参数
	public function getPathInfo(){
		return $this->request->server['path_info'];
	}
	
	//获取地址参数
	public function getQueryString(){
		return $this->request->server['query_string'];
	}
	
	//获取客户端ip
	public function getRemoteAddr(){
		return $this->request->server['remote_addr'];
	}
	
    //获取客户端端口
    public function getRemotePort(){
        return $this->request->server['remote_port'];
    }	
	
	//输出字符串
	public function pre($str){
		$this->response->end($str);
	}
    
	//网址跳转
    public function location($url)
    {
        //发送Http状态码，如500, 404等等
        $this->response->status(302);

        $this->response->header('Location', $url);
    }	
	
	//设置head头
	public function header($val,$type='Content-Type'){
	    $this->response->header($type,$val);
	}	
	
	//设置服务器信息，兼容
	public function setServerValue(){

		$_GET     = $this->getParam();
		
		$_POST    = $this->postParam();  
		
		$_FILES   = $this->filesParam();
		
		$_COOKIE  = $this->cookieParam();
		
        $_SERVER['DOCUMENT_ROOT']            =  $_SERVER['PWD'];
		
		$_SERVER['REQUEST_METHOD']  = $this->getRequestMethod();//请求方式

		$_SERVER['REQUEST_TIME']    =  $this->getRequestTime();//时间
		
		$_SERVER['REQUEST_TIME_FLOAT']  = $this->getRequestTimeFloat();//时间戳浮点数
		
		$_SERVER['REMOTE_ADDR']     = $this->getRemoteAddr();//用户ip
		
		$_SERVER['REMOTE_PORT']     = $this->getRemotePort();//用户端口	
		
		$_SERVER['PATH_INFO']       = $this->getPathInfo();

		$_SERVER['REQUEST_URI']     = $this->getRequestUri();	
			
	    $_SERVER['QUERY_STRING']    = $this->getQueryString();
		
	    $_SERVER['SERVER_HOST']     = $this->getServerAddr();
		
		$_SERVER['SERVER_ADDR']     = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_HOST'] : gethostbyname($_SERVER['SERVER_HOST']);//服务器地址
		
		$_SERVER['SERVER_PORT']     = $this->getServerPort();//服务器端口
		
		$_SERVER['SERVER_PROTOCOL'] = $this->getServerProtocol();//protocol
				
		$_SERVER['SERVER_SOFTWARE'] = $this->getServerSoftware();//SWOOLE标识
		
		$_SERVER['HTTP_HOST']       = $this->getHttpHost();//host
		
        $_SERVER['HTTP_CONNECTION'] = $this->getHttpConnection();
		
        $_SERVER['HTTP_USER_AGENT'] = $this->getHttpUserAgent();//用户ua
		
        $_SERVER['HTTP_ACCEPT']     = $this->getHttpAccept();//accept
        $_SERVER['HTTP_ACCEPT_ENCODING']     =  $this->getHttpAcceptEncoding();//accept-encoding
		
        $_SERVER['HTTP_ACCEPT_LANGUAGE']     =  $this->getHttpAcceptLanguage();
	    
		//unset($_SERVER);
	}
	
	//获取REQUEST
	public function requestParam(){
		return $this->request->request;
	}		
	
	//获取GET
	public function getParam(){
		return $this->request->get;
	}	
	
	//获取POST
	public function postParam(){
		return $this->request->post;
	}		
	
	//获取COOKIE
	public function cookieParam(){
		return $this->request->cookie;
	}		
	
	//获取File参数
	public function filesParam(){
		return $this->request->files;
	}	

	//获取tmpfiles参数
	public function tmpfilesParam(){
		return $this->request->tmpfiles;
	}
	
	
	//启动session
	public function startSession(){
        $this->sessionStatus = 1;
	}

	//暂停session
	public function stopSession(){
        $this->sessionStatus = 0;
	}	
	
	//初始化session
	public function initSession(){
		$this->session = new Session();
		$this->session->start($this->request,$this->response);
	}
	
	//自动保存session
	public function saveSession(){
		if(!empty($this->session))
		     $this->session->end();
	}	
	
	//监听处理
	public function onRequest($request, $response){
		
		//记录一下时间
        $_SERVER['FC_STIME'] = microtime(true);
		
		$this->request   =  $request;
		
		$this->response  =  $response;
		
		$this->setServerValue();
		
		if($this->sessionStatus===1){
		    $this->initSession();//开启start
		}
		
		ob_start();
    
        //MVC模拟器
        try {
            $mvc = GetObj('mvcStart', 'start'); //这个函数不懂，看函数库里的注释
            $mvc->run();
        } catch (\Exception $e) {
            \ErrorShow($e->getMessage()); //打印访问错误
        }
   
		$info=ob_get_contents();
	    
        $this->pre($info);
	}
	
	//读取配置
	public function getConfig(){
		$config = [];
		$file = PATH.'/config.php';
		if(is_file($file)){
			$config = include($file);
		}
		return $config;
	}
	
	
	//判断是不是函数或者匿名函数
    public function isFunc($func){
        if (empty($func) || is_array($func)) {
            return false;
        }
        if ($func instanceof \Closure) {
            return true;
        } else {
            if (function_exists($func)) {
                return true;
            }
        }
        return false;
    }
	
	//运行
	public function start($host='0.0.0.0',$port='8080'){
		
       $http = $this->http =  new \swoole_http_server($host,$port);

       $http->on("start", function ($server) {
           echo "Swoole http server is started at ".$server->port;
       });
  
       $http->on("request",array($this,'onRequest'));

       $http->on('close',array($this, 'onClose'));
	   
	   $http->start();
	}
	
	//关闭
    public function onClose($server, $fd, $reactorId)
    {
         $this->saveSession();
    }	
}