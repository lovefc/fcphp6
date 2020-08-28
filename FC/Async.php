<?php

/**
 * author: lovefc
 * time: 2020/08/28 10:48
 * 进程关闭hook执行
 * 用于脚本关闭后执行脚本
 * fastcgi_finish_request 仅在linux下生效
 * Async::hook(array(new a(), 'b'), array());//面向对象调用
 * Async::hook('SmsService::sendSMS', array(trim($phone), $noticeWords));//面向过程方式调用
 */
 
class Async {
     
    private static $hook_list = array();
	
    private static $hooked = false;
 
 
    /**
     * hook函数fastcgi_finish_request执行
     *
     * @param callback $callback
     * @param array $params
	 *
     * @return void
     */
    public static function hook($callback, $params) {
        self::$hook_list[] = array('callback' => $callback, 'params' => $params);
        if(self::$hooked == false) {
            self::$hooked = true;
            register_shutdown_function(array(__CLASS__, '__run'));
        }
    }
     
    /**
     * 由系统调用
     *
     * @return void
     */
    public static function __run() {
        fastcgi_finish_request();
        if(empty(self::$hook_list)) {
            return;
        }
        foreach(self::$hook_list as $hook) {
            $callback = $hook['callback'];
            $params = $hook['params'];
            call_user_func_array($callback, $params);
        }
    }
}
